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

    namespace Sycamore\Error;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    class Error
    {
        /**
         * Constructs an error from its key.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         * @param string $key The key of the error to construct.
         */
        public static function create(ServiceLocatorInterface& $serviceManager, $key)
        {
            // Grab raw error message.
            $errorMessage = $serviceManager->get("Language")->fetchPhrase($key);
            
            // Grab parameter paths embedded in message.
            $params = preg_split("/{((?:[a-zA-Z]+[\\\/])+[a-zA-Z]+)}/g", $errorMessage);            
            foreach ($params as $param) {
                // Split parameter path up into parts.
                $path = preg_split("/([a-zA-Z]+)/g", $param);
                
                // Set starting value from path and then iterate into final value.
                $value = $serviceManager->get("Config")["Sycamore"];
                foreach ($path as $pathPart) {
                    $value = $value[$pathPart];
                }
                
                // Replace param path with the value.
                $errorMessage = str_replace("{" . $param . "}", strval($value), $errorMessage);
            }
            
            // Return the formatted error message.
            return $errorMessage;
        }
    }
