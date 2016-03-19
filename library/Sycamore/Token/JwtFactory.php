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
    use Sycamore\Stdlib\AbstractFactory;
    use Sycamore\Stdlib\Rand;
    use Sycamore\Token\Jwt;
    
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Factory for constructing JWT tokens.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class JwtFactory extends AbstractFactory
    {
        /**
         * Creates a JWT token from an array, or array-like set of data.
         * 
         * Data form should be similar to:
         * array (
         *   "signMethod" => "HS512",
         *   "key" => <KEY>,
         *   "keyPassphrase" => <KEY_PASSPHRASE>
         *   "tokenLifetime" => 36400,
         *   "registeredClaims" => array ( // OPTIONAL
         *     "iss" => "example.org",
         *     "sub" => "user", 
         *     "aud" => "example.com", // Alternatively: ["example.com", "example.org"].
         *     "iat" => time(),
         *     "nbf" => time(),
         *     "jti" => "12ae2qawd24",
         *   ),
         *   "applicationPayload" => array ( // OPTIONAL
         *     "id" => 1,
         *     "username" => "JohnSmith42",
         *     "email" => "john.smith42@example.com"
         *   ),
         *   "privateClaims" => array ( // OPTIONAL
         *     <CUSTOM_MODULE_NAMESPACE> => array (
         *       <DATA_POINT_KEY> => <DATA_POINT_VALUE>
         *     )
         *   )
         * )
         * 
         * @param array|\Traversable|\ArrayAccess $data
         * 
         * @return \Sycamore\Token\Jwt
         * @throws \DomainException
         */
        public static function create(ServiceLocatorInterface& $serviceManager, $data)
        {
            // Verify provided data is array-like.
            $verifiedData = static::validateData($data);
            
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
            if (Jwt::SIGNERS[$signMethod] !== NULL) {
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
            $domain = $config["domain"]
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
            if (Jwt::SIGNERS[$signMethod]["asymmetric"]) {
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
            $signer = new Jwt::SIGNERS[$signMethod]["class"]();
            $token->sign($signer, $key);
            
            // Return JWT object.
            return (new Jwt($serviceManager, strval($token->getToken())));
        }
    }
    