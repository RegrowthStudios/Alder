<?php
    
    namespace Alder\Token;
    
    use Alder\DiContainer;
    use Alder\Stdlib\Rand;
    
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer;
    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Token;
    
    /**
     * Factory for creating Token objects.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class Factory
    {
        /**
         * Creates a JWT from the provided public and private claims, signed with the
         * provided signer and key or the application defaults.
         *
         * @param array                         $publicClaims  The public claims of the token.
         * @param array                         $privateClaims The private claims of the token.
         * @param int                           $lifetime      The lifetime of the token.
         * @param \Lcobucci\JWT\Signer|null     $signer        If provided, this signer will be used to sign the token.
         * @param \Lcobucci\JWT\Signer\Key|null $key           If provided, this key will be used to sign the token.
         *
         * @return \Lcobucci\JWT\Token
         * @throws \Alder\Token\Exception\KeyException#
         */
        public static function create(array $publicClaims, array $privateClaims, int $lifetime, Signer $signer = null,
                                      Key $key = null) : Token {
            // Grab application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Get signer object from config if not provided by caller.
            $signer = $signer ?: DiContainer::get()->get("token_signer");
            
            // TODO(Matthew): Support passphrases for private key.
            // Get key from config if not provided by caller.
            if (!$key) {
                $keyPath = $config["token"]["private_key"];
                if (!is_readable($keyPath)) {
                    throw new Exception\KeyException(
                        "No key was provided and the configured default key location is invalid."
                    );
                }
                $key = new Key($keyPath);
            }
            
            // Prepare token builder with public claims.
            $time = time();
            $iat = $publicClaims["iat"] ?? $time;
            $domain = $config["domain"];
            $builder = (new Builder())->setIssuer($publicClaims["iss"] ?? $domain)
                                      ->setAudience($publicClaims["aud"] ?? $domain)
                                      ->setIssuedAt($iat)
                                      ->setExpiration($iat + $lifetime)
                                      ->setNotBefore($publicClaims["nbf"] ?? $time)
                                      ->setId($publicClaims["jti"] ?? Rand::getString(12, Rand::ALPHANUMERIC), true);
            if (isset($publicClaims["sub"])) {
                $builder->setSubject($publicClaims["sub"]);
            }
            
            // Prepare builder with private claims.
            foreach ($privateClaims as $label => $value) {
                $builder->set($label, $value);
            }
            
            // Sign token.
            $builder->sign($signer, $key);
            
            // Return token.
            $builder->getToken();
        }
    }
