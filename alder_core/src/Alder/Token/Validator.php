<?php
    
    namespace Alder\Token;
    
    use Alder\DiContainer;
    
    use Lcobucci\JWT\Signer;
    use Lcobucci\JWT\Token;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\ValidationData;
    
    /**
     * Validates JWT's created by Lcobucci's library.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class Validator
    {
        /**
         * Verifies the token's signature is valid, and then validates the claims against the provided validators.
         *
         * $validators form should be similar to:
         * [
         *     "public" => [
         *         "iss" => ["example.com", "example.org"], [Default: Configured application domain.]
         *         "aud" => "example.org", [Default: Configured application domain.]
         *         "currentTime" => time() + 3600, [Default: time()]
         *         "sub" => "user",
         *         "jti" => "12ae2qawd24"
         *     ],
         *     "private" => [
         *         "claim" => "value"
         *     ]
         * ];
         *
         * @param \Lcobucci\JWT\Token           $token      The token to validate.
         * @param array                         $validators The validators to compare claims against.
         * @param \Lcobucci\JWT\Signer|null     $signer     The signing object expected to have been used in creating
         *                                                  the token.
         * @param \Lcobucci\JWT\Signer\Key|null $key        The key used for signing the token.
         *
         * @throws \Alder\Token\Exception\KeyException If no useable key could be acquired.
         *
         * @return bool
         */
        public static function validate(Token $token, array $validators, Signer $signer = null,
                                        Key $key = null) : bool {
            // Grab application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Get signer object from config if not provided by caller.
            $signer = $signer ?: DiContainer::get()->get("token_signer");
            
            // Get key from config if not provided by caller.
            if (!$key) {
                $keyPath = $config["token"]["public_key"];
                if (!is_readable($keyPath)) {
                    throw new Exception\KeyException("No key was provided and the configured default key location is invalid.");
                }
                $key = new Key($keyPath);
            }
            
            // Verify token signature.
            if (!$token->verify($signer, $key)) {
                // Error: Token's signature invalid.
            }
            
            // Prepare validation object.
            $validationData = new ValidationData();
            $validationData->setIssuer($config["domain"]);
            $validationData->setAudience($config["domain"]);
            if (isset($validators["public"])) {
                $publicValidators = $validators["public"];
                if (isset($publicValidators["iss"])) {
                    $validationData->setIssuer($publicValidators["iss"]);
                }
                if (isset($publicValidators["aud"])) {
                    $validationData->setAudience($publicValidators["aud"]);
                }
                if (isset($publicValidators["currentTime"])) {
                    $validationData->setCurrentTime($publicValidators["currentTime"]);
                }
                if (isset($publicValidators["sub"])) {
                    $validationData->setSubject($publicValidators["sub"]);
                }
                if (isset($publicValidators["jti"])) {
                    $validationData->setId($publicValidators["jti"]);
                }
            }
            
            // Validate token's public claims.
            if (!$token->validate($validationData)) {
                // Error: Token's public claims invalid.
            }
            
            // Validate token's private claims, if any to be validated.
            if (isset($validators["private"]) && is_iterable($validators["private"])) {
                $claims = $token->getClaims();
                foreach ($validators["private"] as $label => $expected) {
                    if (!isset($claims[$label])) {
                        continue;
                    }
                    /**
                     * @var \Lcobucci\JWT\Claim $claim
                     */
                    $claim = $claims[$label];
                    
                    if (method_exists($claim, "validate")) {
                        $result = $claim->validate($expected);
                    } else {
                        $result = $claim->getValue() === $expected;
                    }
                    
                    if (!$result) {
                        // Error: Token's private claims invalid.
                    }
                }
            }
            
            // Token is valid!
            return true;
        }
    }
