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
    
    /**
     * Encapsulates public-facing error generation for application.
     */
    class ErrorManager
    {
        /**
         * Constants for determining if errors should be cleared on returning them from getErrors().
         */
        const KEEP_ERRORS = 0;
        const DELETE_ERRORS = 1;
        
        /**
         * Instance of error manager.
         * 
         * @var \Sycamore\ErrorManager
         */
        protected static $instance = NULL;
        
        /**
         * Error collection.
         * 
         * @var array
         */
        protected $errors = [];
        
        /**
         * Protected constructor. Use {@link getInstance()} instead.
         */
        protected function __construct(ServiceLocatorInterface& $serviceManager)
        {            
        }
        
        /**
         * Adds an error.
         * 
         * @param string $errorType The type of error to be added.
         * @param string $errorMessageKey The key of the error message to fetch from the language files.
         */
        public function addError($errorType, $errorMessageKey)
        {
            // Grab raw error message.
            $errorMessage = "$errorMessageKey"; // TODO(Matthew): Implement language system.
            throw new \RuntimeException("NOT IMPLEMENTED, GET ON IT MATTHEW!");
            
            // Grab parameter paths embedded in message.
            $params = preg_split("/{((?:[a-zA-Z]+[\\\/])+[a-zA-Z]+)}/g", $errorMessage);            
            foreach ($params as $param) {
                // Split parameter path up into parts.
                $path = preg_split("/([a-zA-Z]+)/g", $param);
                
                // Set starting value from path and then iterate into final value.
                $value = $this->serviceManager->get("Config");
                foreach ($path as $pathPart) {
                    $value = $value[$pathPart];
                }
                
                // Replace param path with the value.
                $errorMessage = str_replace("{" . $param . "}", strval($value), $errorMessage);
            }
            
            // Insert the formatted error message into errors array.
            $this->errors[] = [ "type" => $errorType, "message" => $errorMessage ];
        }
        
        /**
         * Ascertains if one or more errors have been added.
         * 
         * @return boolean
         */
        public static function hasError()
        {
            return !empty(self::$errors);
        }
        
        /**
         * Returns the collection of errors stored in the ErrorManager.
         * 
         * @param int $clearErrors Whether to clear errors after fetching them or not.
         * 
         * @return array
         */
        public function getErrors($clearErrors = self::KEEP_ERRORS)
        {
            $errors = $this->errors();
            if ($clearErrors == self::DELETE_ERRORS) {
                $this->clearErrors();
            }
            return $errors;
        }
        
        /**
         * Clears out all existing errors.
         */
        public function clearErrors()
        {
            $this->errors = [];
        }
        
        /**
         * Gets instance of the error manager, preparing it if not already existent.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         */
        public static function getInstance(ServiceLocatorInterface& $serviceManager)
        {
            if (!static::$instance) {
                static::$instance = new ErrorManager($serviceManager);
            }
            return static::$instance;
        }
    }
