<?php
    
    namespace Alder\PublicAuthentication\Visitor\Cookie;
    
    /**
     * Provides an interface for the basic cookie data wrapper.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    interface CookieInterface extends \ArrayAccess
    {
        /**
         * Initialises the cookie, populating it with the data provided.
         *
         * @param array $data The data to initialise the cookie with.
         *
         * @return \Alder\PublicAuthentication\Visitor\Cookie\CookieInterface|null Returns self if initialised, null if
         *                                                                  already initialised.
         */
        public function initialise(array $data) : ?CookieInterface;
        
        /**
         * Determines in the cookie has been modified in any way.
         *
         * @return bool True if the cookie's data has been changed, once otherwise.
         */
        public function hasChanged() : bool;
    }
