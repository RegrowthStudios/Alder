<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
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
    
    use Lcobucci\JWT\Builder;
    use Lcobucci\JWT\Signer\Key;
    
    use Zend\Math\Rand;
    
    /**
     * Factory for constructing JWT tokens.
     */
    class JwtFactory extends AbstractFactory
    {
        const SIGNERS = array (
            "HS256" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha256", "asymmetric" => false),
            "HS384" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha384", "asymmetric" => false),
            "HS512" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\Hmac\\Sha512", "asymmetric" => false),
            "RS256" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha256", "asymmetric" => true),
            "RS384" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha384", "asymmetric" => true),
            "RS512" => array ( "loc" => "\\Lcobucci\\JWT\\Signer\\RSA\\Sha512", "asymmetric" => true),
        );
        
        // TODO(Matthew): Update Application::getConfig() calls to new scheme.
        /**
         * Creates a JWT token from an array, or array-like set of data.
         * 
         * Data form should be similar to:
         * array (
         *   "signMethod" => "HS512",
         *   "privateKey" => <PRIVATE_KEY>,
         *   "privateKeyPassphrase" => <PRIVATE_KEY_PASSPHRASE>
         *   "tokenLifetime" => 36400,
         *   "sub" => "user", // OPTIONAL
         *   "aud" => "example.com", // OPTIONAL
         *   "iat" => time(), // OPTIONAL
         *   "nbf" => time(), // OPTIONAL
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
         * @return type
         * @throws \DomainException
         */
        public static function create($data)
        {
            $data = static::validateData($data);
            
            $privateKey = Application::getConfig()->security->tokenPrivateKey;
            if (isset($data["privateKey"])) {
                $privateKey = $data["privateKey"];
            }
            if (!$privateKey || $privateKey == CHANGE_THIS) {
                throw new \InvalidArgumentException("The private key provided via data or application config is invalid.");
            }
            
            $signMethod = Application::getConfig()->security->tokenHashAlgorithm;
            if (isset($data["signMethod"])) {
                $signMethod = $data["signMethod"];
            }
            if (!isset(self::SIGNERS[$signMethod])) {
                throw new \InvalidArgumentException("The sign method provided via data or application config is an invalid method.");
            }
            
            if (!isset($data["tokenLifetime"])) {
                throw new \DomainException("Token factory expects a token lifetime to be specified.");
            }
            
            $time = time();
            
            $token = (new Builder())->setIssuer(Application::getConfig()->domain)
                                       ->setAudience(isset($data["aud"]) ? $data["aud"] : Application::getConfig()->domain)
                                       ->setIssuedAt(isset($data["iat"]) ? $data["iat"] : $time)
                                       ->setExpiration((isset($data["iat"]) ? $data["iat"] : $time) + $data["tokenLifetime"])
                                       ->setNotBefore(isset($data["nbf"]) ? $data["nbf"] : $time)
                                       ->setId(Rand::getString(12, NULL, true), true);
            
            if (isset($data["sub"])) {
                $token->setSubject($data["sub"]);
            }
            
            if (isset($data["applicationPayload"])) {
                $token->set(Application::getConfig()->domain, $data["applicationPayload"]);
            }
            
            if (isset($data["privateClaims"])) {
                if (is_array($data["privateClaims"])) {
                    foreach ($data["privateClaims"] as $key => $value) {
                        $token->set($key, $value);
                    }
                } else {
                    throw new \InvalidArgumentException("Token factory expects private claims to be in array (key, value) form.");
                }
            }
            
            $key = $privateKey;
            
            $passphrase = NULL;
            if (self::SIGNERS[$signMethod]["asymmetric"]) {
                if (Application::getConfig()->security->tokenPrivateKeyPassphrase !== CHANGE_THIS) {
                    $passphrase = Application::getConfig()->security->tokenPrivateKeyPassphrase;
                }
                if (isset($data["privateKeyPassphrase"])) {
                    $passphrase = $data["privateKeyPassphrase"];
                }
                $key = new Key($privateKey, $passphrase);
            }
            
            $signer = new self::SIGNERS[$signMethod]["loc"]();
            $token->sign($signer, $key);
            
            return $token->getToken();
        }
    }
    