<?php
    namespace Sycamore\Token;

    use Sycamore\Stdlib\ArrayUtils;

    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Parser;
    use Lcobucci\JWT\ValidationData;

    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Wrapper object for Lcobucci's Token object.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Jwt
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
         * The JWT token for the instance.
         *
         * @var \Lcobucci\JWT\Token
         */
        protected $token = NULL;

        /**
         * The state of validation for the current token.
         *
         * @var null|integer
         */
        protected $state = NULL;

        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;

        /**
         * Constructs a token object from the token string if given.
         *
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         * @param string $token The token to initialise this instance with.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager, $token = NULL)
        {
            $this->serviceManager = $serviceManager;
            if (!is_null($token)) {
                $this->token = (new Parser())->parse((string) $token);
            }
        }

        /**
         * Sets this Jwt insance's token as constructed from the given token string.
         *
         * @param string $token The token to set.
         */
        public function setToken($token)
        {
            $this->state = NULL;
            $this->token = (new Parser())->parse((string) $token);
        }

        /**
         * Returns the token's payload in string form.
         *
         * @return string The payload of the current token.
         */
        public function getPayload()
        {
            return $this->token->getPayload();
        }

        /**
         * Gets the requested head item if it exists.
         *
         * @param string $name The name of the header to fetch.
         * @param mixed $default A default value to return if no header exists.
         *
         * @return mixed The item at the specified header location, or the specified default.
         *
         * @throws \OutOfBoundsException If no item exists at the specified header location and no default was specified.
         */
        public function getHeader($name, $default = NULL)
        {
            try {
                return $this->token->getHeader($name, $default);
            } catch (\OutOfBoundsException $ex) {
                throw $ex;
            }
        }

        /**
         * Returns all head items in this token.
         *
         * @return array The headers of the current token.
         */
        public function getHeaders()
        {
            return $this->token->getHeaders();
        }

        /**
         * Gets the requested claim item if it exists.
         *
         * @param string $name The name of the claim to fetch.
         * @param mixed $default The default value to return if no claim is found.
         *
         * @return mixed The value of the claim fetched or the default value if no claim value exists at the specified location.
         *
         * @throws \OutOfBoundsException If no value exists at the specified claim location, and no default was specified.
         */
        public function getClaim($name, $default = NULL)
        {
            try {
                return $this->token->getClaim($name, $default);
            } catch (\OutOfBoundsException $ex) {
                throw $ex;
            }
        }

        /**
         * Returns all claim items in this token.
         *
         * @return array The claims of the current token.
         */
        public function getClaims()
        {
            return $this->token->getClaims();
        }

        /**
         * Determines if the given claim exists in this token.
         *
         * @param string $name The claim item to determine the existence of.
         *
         * @return bool True if the item exists, false otherwise.
         */
        public function hasClaim($name)
        {
            return $this->token->hasClaim($name);
        }

        /**
         * Determines if the given head item exists in this token.
         *
         * @param string $name The header item to determine the existence of.
         *
         * @return bool True if the item exists, false otherwise.
         */
        public function hasHeader($name)
        {
            return $this->token->hasHeader($name);
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
         * @return int Jwt::VALID if the token is valid, otherwise Jwt::INVALID_SIGNATURE if signature are invalid or Jwt::INVALID_PUBLIC_CLAIMS if public claims are invalid.
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
            if (!isset(self::SIGNERS[$signMethod])) {
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
                $this->state = self::INVALID_SIGNATURE;
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

        /**
         * Returns the token as a string.
         *
         * @return string The string form of the current token.
         */
        public function __toString()
        {
            return strval($this->token);
        }
    }
