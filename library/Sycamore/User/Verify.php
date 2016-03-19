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

    namespace Sycamore\User;

    use Sycamore\Stdlib\ArrayUtils;
    use Sycamore\Token\JwtFactory;
    use Sycamore\Token\Jwt;
    
    /**
     * Verify has functions related to creation and checking of verification tokens.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Verify
    {
        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;
        
        /**
         * Prepares the sercurity utility by injecting the service manager.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            $this->serviceManager = $serviceManager;
        }
        
        /**
         * Constructs a verification token.
         * 
         * @param int $userId The ID of the user whom this token applies to.
         * @param array|\Traversable $items The items pertaining to this verification. E.g. a delete key.
         * @param int $tokenLifetime The lifetime of the constructed verification token.
         * @param string $purpose The purpose of this verification token.
         * 
         * @return \Sycamore\Token\Jwt A JWT object containing the token string.
         */
        public function create($userId, $items, $tokenLifetime = 86400 /* 24 Hours */, $purpose = "verification")
        {
            $vaildatedItemsToVerify = ArrayUtils::validateArrayLike($itemsToVerify, get_class($this), true);
            
            return JwtFactory::create($this->serviceManager, [
                "tokenLifetime" => $tokenLifetime,
                "registeredClaims" => [
                    "sub" => (string) $purpose
                ],
                "applicationPayload" => array_merge($items, [
                    "id" => $userId
                ])
            ]);
        }
        
        /**
         * Verifies the verification token.
         *
         * @param int $userId The ID of the user whom this token applies to.
         * @param string $token The token to verify.
         * @param array $itemsExpected The expected items if token is to be verfied. E.g. delete key.
         * @param string $purpose The purpose of this verification token.
         * 
         * @return bool|array The application payload if the token is valid, otherwise false.
         */
        public function verify($userId, $token, $itemsExpected, $purpose = "verification")
        {
            $token = new Jwt($token);
            // Ensure token is generally valid as per public claims.
            if (!$token->validate([
                "sub" => $purpose
            ])) {
                return false;
            }
            
            // Get application payload.
            $payload = $token->getPayload();
            $applicationPayload = $payload["applicationPayload"];
            
            // If application payload and expected items + user ID are equivalent, then token is truly verified.
            if (empty( ArrayUtils::xorArrays( array_merge($itemsExpected, [
                                    "id" => $userId
                                ]), $applicationPayload))) {
                return $applicationPayload;
            }
            
            return false;
        }
    }