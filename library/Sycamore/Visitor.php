<?php

/**
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
 *
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License 3.0
 */

    namespace Sycamore;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    class Visitor
    {
        /**
         * Stores data pertaining to the visitor.
         *
         * @var array 
         */
        protected $data;
        
        /**
         * Grabs any existing session data if visitor is logged in and stores it.
         * 
         * @param ServiceLocatorInterface $serviceManager The service manager for this application instance.
         * 
         * @return \Sycamore\Visitor Self
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            // Grab the user session helper.
            $userSession = $serviceManager->get("Sycamore\User\Session");
            
            // Grab token payload if SLIS exists.
            $tokenPayload = [];
            if (!$userSession->acquire($tokenPayload)) {
                $this->data["isLoggedIn"] = false;
                return;
            }
            
            // Visitor is logged in, add data here.
            $this->data = array_merge($tokenPayload["applicationPayload"], [
                "isLoggedIn" => true
            ]);
        }
        
        /**
         * Determines if the visitor is logged in.
         * 
         * @return bool True if logged in, false otherwise.
         */
        public function isLoggedIn()
        {
            return $this->data["isLoggedIn"];
        }
        
        /**
         * Gets the specified property of the visitor. E.g. "id", "superUser" etc..
         * 
         * @param string $property The property to get.
         * 
         * @return mixed The value of the given property if it exists.
         * 
         * @throws \InvalidArgumentException if $property is not a valid property key.
         */
        public function get($property)
        {
            if (isset($this->data[$property])) {
                return $this->data[$property];
            }
            
            // TODO(Matthew): Add functions to fetch some common properties of the visiting user, for performance purposes?
            $fetchFunc = "fetch" . ucfirst($property);
            if (method_exists($this, $fetchFunc)) {
                $result = $this->{$fetchFunc}();
                if (!is_null($result)) {
                    $this->data[$property] = $result;
                    return $result;
                }
            }
            
            throw new \InvalidArgumentException("The property, $property, is not valid for Visitor.");
        }
    }
