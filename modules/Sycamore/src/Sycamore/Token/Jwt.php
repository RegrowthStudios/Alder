<?php

/*
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    namespace Sycamore\Token;

    use Sycamore\Application;
    use Sycamore\Stdlib\ArrayUtils;

    use Lcobucci\JWT\Signer\Key;
    use Lcobucci\JWT\Parser;
    use Lcobucci\JWT\ValidationData;

    use Zend\ServiceManager\ServiceLocatorInterface;

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
         * @param string $token
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
         * @param string $token
         */
        public function setToken($token)
        {
            $this->state = NULL;
            $this->token = (new Parser())->parse((string) $token);
        }

        /**
         * Returns the token's payload in string form.
         *
         * @return string
         */
        public function getPayload()
        {
            return $this->token->getPayload();
        }

        /**
         * Gets the requested head item if it exists.
         *
         * @param string $name
         * @param mixed $default
         *
         * @return mixed
         *
         * @throws OutOfBoundsException
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
         * @return array
         */
        public function getHeaders()
        {
            return $this->token->getHeaders();
        }

        /**
         * Gets the requested claim item if it exists.
         *
         * @param string $name
         * @param mixed $default
         *
         * @return mixed
         *
         * @throws OutOfBoundsException
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
         * @return array
         */
        public function getClaims()
        {
            return $this->token->getClaims();
        }

        /**
         * Determines if the given claim exists in this token.
         *
         * @param string $name
         *
         * @return bool
         */
        public function hasClaim($name)
        {
            return $this->token->hasClaim($name);
        }

        /**
         * Determines if the given head item exists in this token.
         *
         * @param string $name
         *
         * @return bool
         */
        public function hasHeader($name)
        {
            return $this->token->hasHeader($name);
        }

        /**
         * Verifies the token's signature is valid, and then validates the public claims against the provided validators.
         *
         * Data form should be similar to:
         * array (
         *   "signMethod" => "HS512",
         *   "key" => <KEY>,
         *   "validators" => array ( // OPTIONAL
         *     "iss" => "example.com", // Alternatively: ["example.com", "example.org"]
         *     "aud" => "example.org",
         *     "currentTime" => time() + 3600,
         *     "sub" => "user",
         *     "jti" => "12ae2qawd24"
         *   )
         * );
         *
         * @param array|\Traversable $data
         *
         * @return integer
         *
         * @throws \InvalidArgumentException
         */
        public function validate($data)
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
            if (self::SIGNERS[$signMethod] == NULL) {
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
            if (isset($verifiedData["validators"])) {
                $validators = $verifiedData["validators"];
                $validationFilters->setIssuer(isset($validators["iss"]) ? $validators["iss"] : $config["domain"]);
                $validationFilters->setAudience(isset($validators["aud"]) ? $validators["aud"] : $config["domain"]);
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
         * @return string
         */
        public function __toString()
        {
            return strval($this->token);
        }
    }
