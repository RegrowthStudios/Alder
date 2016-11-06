<?php
    
    namespace Alder\Token;
    
    use Alder\Container;
    use Alder\Token\Token;
    use Alder\Token\Builder;
    
    use Lcobucci\JWT\Signer\Key;
    
    use Zend\Json\Json;
    
    /**
     * Factory for creating Token objects.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class TokenFactory
    {
        /**
         * Creates a JWT from an array, or array-like set of data.
         *
         * Data form should be similar to:
         * [
         *   "signMethod" => "HS512",
         *   "key" => <KEY>,
         *   "keyPassphrase" => <KEY_PASSPHRASE>
         *   "tokenLifetime" => 36400,
         *   "registeredClaims" => [ // OPTIONAL
         *     "iss" => "example.org",
         *     "sub" => "user",
         *     "aud" => "example.com", // Alternatively: ["example.com", "example.org"].
         *     "iat" => time(),
         *     "nbf" => time(),
         *     "jti" => "12ae2qawd24",
         *   ],
         *   "applicationPayload" => [ // OPTIONAL
         *     "id" => 1,
         *     "username" => "JohnSmith42",
         *     "email" => ...
         *   ],
         *   "privateClaims" => [ // OPTIONAL
         *     <CUSTOM_MODULE_NAMESPACE> => [
         *       <DATA_POINT_KEY> => <DATA_POINT_VALUE>
         *     ]
         *   ]
         * ]
         *
         * @param array|\Traversable|\ArrayAccess $data The data to create the token from.
         *
         * @return \Alder\Token\Token The resulting token.
         *
         * @throws \InvalidArgumentException If $data is of invalid type, or if signing method is invalid, or if
         *                                   private claims are in invalid form.
         * @throws \DomainException If the token's lifetime is not specified.
         */
        public static function create(array $data) : Token {
            // Grab application config.
            $config = Container::get()->get("config")["alder"];
            // Acquire private key or fail.
            $privateKey = $config["public_authentication"]["token"]["private_key"];
            if (isset($data["key"])) {
                $privateKey = $data["key"];
            }
            if (!$privateKey || $privateKey == DEFAULT_VAL) {
                throw new \InvalidArgumentException("The key provided via data or application config is invalid.");
            }
            // Acquire signing method or fail.
            $signMethod = $config["security"]["token"]["hash_algorithm"];
            if (isset($data["signMethod"])) {
                $signMethod = $data["signMethod"];
            }
            if (Token::SIGNERS[$signMethod] == null) {
                throw new \InvalidArgumentException("The sign method provided via data or application config is an invalid method.");
            }
            // Fail if no lifetime specified for token.
            if (!isset($data["tokenLifetime"])) {
                throw new \DomainException("Token factory expects a token lifetime to be specified.");
            }
            $registeredClaims = [];
            if (isset($data["registeredClaims"])) {
                $registeredClaims = $data["registeredClaims"];
            }
            // Construct token and registered claims.
            $time = time();
            $domain = $config["domain"];
            $token = (new Builder())->setIssuer(isset($registeredClaims["iss"]) ? $registeredClaims["iss"] : $domain)
                                    ->setAudience(isset($registeredClaims["aud"]) ? $registeredClaims["aud"] : $domain)
                                    ->setIssuedAt(isset($registeredClaims["iat"]) ? $registeredClaims["iat"] : $time)
                                    ->setExpiration(isset($registeredClaims["iat"]) ? $registeredClaims["iat"]
                                                                                      + $data["tokenLifetime"] : $time
                                                                                                                 + $data["tokenLifetime"])
                                    ->setNotBefore(isset($registeredClaims["nbf"]) ? $registeredClaims["nbf"] : $time)
                                    ->setId((isset($registeredClaims["jti"]) ? $registeredClaims["jti"] : Rand::getString(12,
                                                                                                                          Rand::ALPHANUMERIC,
                                                                                                                          true)),
                                        true);
            // Set subject if specified.
            if (isset($registeredClaims["sub"])) {
                $token->setSubject($registeredClaims["sub"]);
            }
            // Add application payload if provided.
            if (isset($data["applicationPayload"])) {
                $token->set($domain, Json::encode($data["applicationPayload"]));
            }
            // Add additional private claims if specified.
            if (isset($data["privateClaims"])) {
                if (is_array($data["privateClaims"])) {
                    foreach ($data["privateClaims"] as $key => $value) {
                        $token->set($key, $value);
                    }
                } else {
                    throw new \InvalidArgumentException("Token factory expects private claims to be in array (key, value) form.");
                }
            }
            // Create private key object if signing method is asymmetric, otherwise use private key string.
            $key = $privateKey;
            $passphrase = null;
            if (Token::SIGNERS[$signMethod]["asymmetric"]) {
                $defaultPrivateKeyPassphrase = $config["public_authentication"]["token"]["private_key_passphrase"];
                if ($defaultPrivateKeyPassphrase !== DEFAULT_VAL) {
                    $passphrase = $defaultPrivateKeyPassphrase;
                }
                if (isset($data["keyPassphrase"])) {
                    $passphrase = $data["keyPassphrase"];
                }
                $key = new Key($privateKey, $passphrase);
            }
            // Sign token.
            $signerClassName = Token::SIGNERS[$signMethod]["class"];
            $signer = new $signerClassName();
            $token->sign($signer, $key);
            
            // Return JWT object.
            return $token->getToken();
        }
    }
