<?php
    
    namespace Alder\Token;
    
    use Alder\Container;
    use Alder\Stdlib\ArrayUtils;
    
    use Lcobucci\JWT\Token as LcobucciToken;
    use Lcobucci\JWT\Signer\Key;
    
    /**
     * Wrapper for Lcobucci's Token class.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class Token extends LcobucciToken
    {
        /**
         * Consts returns on calling validate().
         */
        const INVALID_SIGNATURE     = -2;
        
        const INVALID_PUBLIC_CLAIMS = -1;
        
        const VALID                 = 1;
        
        /**
         * Supported signing methods.
         */
        const SIGNERS = ["HS256" => ["class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha256", "asymmetric" => false],
                         "HS384" => ["class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha384", "asymmetric" => false],
                         "HS512" => ["class" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha512", "asymmetric" => false],
                         "RS256" => ["class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha256", "asymmetric" => true],
                         "RS384" => ["class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha384", "asymmetric" => true],
                         "RS512" => ["class" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha512", "asymmetric" => true],];
        
        /**
         * The state of validation for this token instance.
         *
         * @var NULL|integer
         */
        protected $state = null;
        
        /**
         * Verifies the token's signature is valid, and then validates the public claims against the provided
         * validators.
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
         * @return int Token::VALID if the token is valid, otherwise Token::INVALID_SIGNATURE if signature are invalid
         *             or Token::INVALID_PUBLIC_CLAIMS if public claims are invalid.
         *
         * @throws \InvalidArgumentException If data related to signing of token is invalid.
         */
        public function validate($data = []) {
            // If state of token is already known, just send previous result.
            if (!is_null($this->state)) {
                return $this->state;
            }
            // Validate provided data.
            $verifiedData = ArrayUtils::validateArrayLike($data, get_class($this));
            // Grab application config.
            $config = Container::get()->get("config")["alder"];
            // Acquire signing method or fail.
            $signMethod = $config["public_authentication"]["token"]["hash_algorithm"];
            if (isset($verifiedData["signMethod"])) {
                $signMethod = $verifiedData["signMethod"];
            }
            if (!array_key_exists($signMethod, self::SIGNERS)) {
                throw new \InvalidArgumentException("The sign method provided via data or application config is an invalid method.");
            }
            // Acquire public key or fail.
            $key = $config["public_authentication"]["token"]["private_key"];
            if (self::SIGNERS[$signMethod]["asymmetric"]) {
                $key = $config["public_authentication"]["token"]["public_key"];
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
            if (!$this->verify($signer, $key)) {
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
            if (!parent::validate($validationFilters)) {
                $this->state = self::INVALID_PUBLIC_CLAIMS;
                
                return $this->state;
            }
            // Token is valid!
            $this->state = self::VALID;
            
            return $this->state;
        }
    }
