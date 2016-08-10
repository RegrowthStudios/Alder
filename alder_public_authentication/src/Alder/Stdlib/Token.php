<?php
    namespace Alder\Stdlib;
    
    use Alder\Stdlib\ArrayUtils;
    
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    
    /**
     * Helpers for creating and validating JSON web tokens.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Token
    {
        /**
         * Consts returns on calling validate().
         */
        const INVALID_SIGNATURE = -2;
        const INVALID_PUBLIC_CLAIMS = -1;
        const VALID = 1;
        
        /**
         * Supported signing methods.
         */
        const SIGNERS = [
            "HS256" => [ "class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha256", "asymmetric" => false],
            "HS384" => [ "class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha384", "asymmetric" => false],
            "HS512" => [ "class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha512", "asymmetric" => false],
            "RS256" => [ "class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha256",  "asymmetric" =>  true],
            "RS384" => [ "class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha384",  "asymmetric" =>  true],
            "RS512" => [ "class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha512",  "asymmetric" =>  true],
        ];
        
        /**
         * Creates a JWT token from an array, or array-like set of data.
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
         * @return \Lcobucci\JWT\Token The resulting token.
         * 
         * @throws \InvalidArgumentException If $data is of invalid type, or if signing method is invalid, or if private claims are in invalid form.
         * @throws \DomainException If the token's lifetime is not specified.
         */
        public static function create(ServiceLocatorInterface& $serviceManager, $data)
        {
            // Verify provided data is array-like.
            try {
                $verifiedData = ArrayUtils::validateArrayLike($data, self::class, false);
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            // Grab application config.
            $config = $serviceManager->get("Config")["Sycamore"];
            // Acquire private key or fail.
            $privateKey = $config["security"]["tokenPrivateKey"];
            if (isset($verifiedData["key"])) {
                $privateKey = $verifiedData["key"];
            }
            if (!$privateKey || $privateKey == DEFAULT_VAL) {
                throw new \InvalidArgumentException("The key provided via data or application config is invalid.");
            }
            // Acquire signing method or fail.
            $signMethod = $config["security"]["tokenHashAlgorithm"];
            if (isset($verifiedData["signMethod"])) {
                $signMethod = $verifiedData["signMethod"];
            }
            if (self::SIGNERS[$signMethod] == NULL) {
                throw new \InvalidArgumentException("The sign method provided via data or application config is an invalid method.");
            }
            // Fail if no lifetime specified for token.
            if (!isset($verifiedData["tokenLifetime"])) {
                throw new \DomainException("Token factory expects a token lifetime to be specified.");
            }
            $registeredClaims = [];
            if (isset($verifiedData["registeredClaims"])) {
                $registeredClaims = $verifiedData["registeredClaims"];
            }
            // Construct token and registered claims.
            $time = time();
            $domain = $config["domain"];
            $token = (new Builder())->setIssuer(     isset($registeredClaims["iss"]) ? $registeredClaims["iss"] : $domain)
                                    ->setAudience(   isset($registeredClaims["aud"]) ? $registeredClaims["aud"] : $domain)
                                    ->setIssuedAt(   isset($registeredClaims["iat"]) ? $registeredClaims["iat"] : $time)
                                    ->setExpiration( isset($registeredClaims["iat"]) ? $registeredClaims["iat"] : $time + $verifiedData["tokenLifetime"])
                                    ->setNotBefore(  isset($registeredClaims["nbf"]) ? $registeredClaims["nbf"] : $time)
                                    ->setId(        (isset($registeredClaims["jti"]) ? $registeredClaims["jti"] : Rand::getString(12, Rand::ALPHANUMERIC, true)), true);
            // Set subject if specified.
            if (isset($registeredClaims["sub"])) {
                $token->setSubject($registeredClaims["sub"]);
            }
            // Add application payload if provided.
            if (isset($verifiedData["applicationPayload"])) {
                $token->set($domain, $verifiedData["applicationPayload"]);
            }
            // Add additional private claims if specified.
            if (isset($verifiedData["privateClaims"])) {
                if (is_array($verifiedData["privateClaims"])) {
                    foreach ($verifiedData["privateClaims"] as $key => $value) {
                        $token->set($key, $value);
                    }
                } else {
                    throw new \InvalidArgumentException("Token factory expects private claims to be in array (key, value) form.");
                }
            }
            // Create private key object if signing method is asymmetric, otherwise use private key string.
            $key = $privateKey;
            $passphrase = NULL;
            if (self::SIGNERS[$signMethod]["asymmetric"]) {
                $defaultPrivateKeyPassphrase = $config["security"]["tokenPrivateKeyPassphrase"];
                if ($defaultPrivateKeyPassphrase !== DEFAULT_VAL) {
                    $passphrase = $defaultPrivateKeyPassphrase;
                }
                if (isset($verifiedData["keyPassphrase"])) {
                    $passphrase = $verifiedData["keyPassphrase"];
                }
                $key = new Key($privateKey, $passphrase);
            }
            // Sign token.
            $signerClassName = self::SIGNERS[$signMethod]["class"];
            $signer = new $signerClassName();
            $token->sign($signer, $key);
            // Return JWT object.
            return (new Jwt($serviceManager, strval($token->getToken())));
        }
        
        /**
         * Verifies the token's signature is valid, and then validates the public claims against the provided validators.
         *
         * Data form should be similar to:
         * [
         *   "signMethod" => "HS512",
         *   "key" => <KEY>,
         *   "validators" => [
         *     "iss" => "example.com", // Alternatively: ["example.com", "example.org"]
         *     "aud" => "example.org",
         *     "currentTime" => time() + 3600,
         *     "sub" => "user",
         *     "jti" => "12ae2qawd24"
         *   ]
         * ];
         *
         * @param array|\Traversable $data The data to validate against.
         *
         * @return int Token::VALID if the token is valid, otherwise Token::INVALID_SIGNATURE if signature are invalid or Token::INVALID_PUBLIC_CLAIMS if public claims are invalid.
         *
         * @throws \InvalidArgumentException If data related to signing of token is invalid.
         */
        public function validate($data = [])
        {
            // If state of token is already known, just send previous result.
            if (!is_null($this->state)) {
                return $this->state;
            }
            // Validate provided data.
            $verifiedData = ArrayUtils::validateArrayLike($data, get_class($this));
            // Grab application config.
            $config = $this->serviceManager->get("Config")["Sycamore"];
            // Acquire signing method or fail.
            $signMethod = $config["security"]["tokenHashAlgorithm"];
            if (isset($verifiedData["signMethod"])) {
                $signMethod = $verifiedData["signMethod"];
            }
            if (!array_key_exists($signMethod, self::SIGNERS)) {
                throw new \InvalidArgumentException("The sign method provided via data or application config is an invalid method.");
            }
            // Acquire public key or fail.
            $key = $config["security"]["tokenPrivateKey"];
            if (self::SIGNERS[$signMethod]["asymmetric"]) {
                $key = $config["security"]["tokenPublicKey"];
            }
            if (isset($verifiedData["key"])) {
                $key = $verifiedData["key"];
            }
            if (!$key || $key == DEFAULT_VAL) {
                throw new \InvalidArgumentException("The key provided via data or application config is invalid.");
            }
            if (self::SIGNERS[$signMethod]["asymmetric"]) {
                $key = new Key($key);
            }
            // Verify token.
            $signerClass = self::SIGNERS[$signMethod]["class"];
            $signer = new $signerClass();
            if (!$this->token->verify($signer, $key)) {
                $this->state = static::INVALID_SIGNATURE;
                return $this->state;
            }
            // Prepare validation object.
            $validationFilters = new ValidationData();
            $validationFilters->setIssuer($config["domain"]);
            $validationFilters->setAudience($config["domain"]);
            if (isset($verifiedData["validators"])) {
                $validators = $verifiedData["validators"];
                if (isset($validators["iss"])) {
                    $validationFilters->setIssuer($validators["iss"]);
                }
                if (isset($validators["aud"])) {
                    $validationFilters->setAudience($validators["aud"]);
                }
                if (isset($validators["currentTime"])) {
                    $validationFilters->setCurrentTime($validators["currentTime"]);
                }
                if (isset($validators["sub"])) {
                    $validationFilters->setSubject($validators["sub"]);
                }
                if (isset($validators["jti"])) {
                    $validationFilters->setId($validators["jti"]);
                }
            }
            // If token doesn't validate, fail.
            if (!$this->token->validate($validationFilters)) {
                $this->state = self::INVALID_PUBLIC_CLAIMS;
                return $this->state;
            }
            // Token is valid!
            $this->state = self::VALID;
            return $this->state;
        }
    }
