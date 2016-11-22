<?php
    
    namespace Alder\Error;
    
    use Alder\DiContainer;
    
    /**
     * Provides function for retrieving error strings.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Error
    {
        protected static $language = null;
        
        /**
         * Retrieves the error string for the provided error code.
         *
         * @param int|string $code The error code to retrieve the error string for.
         *
         * @return string|bool Error string on successful retrieval, false otherwise.
         * @throws \InvalidArgumentException If non-numeric code provided.
         */
        public static function retrieveString($code) {
            if (!self::$language) {
                self::$language = DiContainer::get()->get("AlderLanguageData");
            }
            if (!is_numeric($code)) {
                throw new \InvalidArgumentException("Invalid error code provided.");
            }
            
            return isset(self::$language["$code"]) ? self::$language["$code"] : false;
        }
    }
