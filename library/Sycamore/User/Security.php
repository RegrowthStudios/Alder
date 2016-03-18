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
    
    use Sycamore\Application;
    
    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Security holds functions for ensuring the security of the user experience.
     */
    class Security
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
         * Return hashed password.
         *
         * @var string $password
         *
         * @return string
         */
        public function hashPassword($password)
        {
            return password_hash($password, PASSWORD_DEFAULT, ["cost" => $this->serviceManager->get("Config")["Sycamore"]["security"]["passwordHashingStrength"] ]);
        }
        
        /**
         * Verifies given password is the same as given hash.
         *
         * @var string $password
         * @var string $hash
         *
         * @return boolean
         */
        public function verifyPassword($password, $hash)
        {
            return password_verify($password, $hash);
        }
        
        /**
         * Verifies if the given password needs rehashing.
         *
         * @var string $password
         *
         * @return boolean
         */
        public function passwordNeedsRehash($password)
        {
            return password_needs_rehash($password, PASSWORD_DEFAULT, [ 'cost' => $this->serviceManager->get("Config")["Sycamore"]["security"]["passwordHashingStrength"] ]);
        }
    }