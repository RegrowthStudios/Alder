<?php
    
    namespace Alder\PublicAuthentication\Visitor\Cookie;
    
    use Alder\PublicAuthentication\Visitor\Cookie\Cookie;
    
    /**
     * Provides a wrapper for the log-in session cookie of a user.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class UserSessionCookie extends Cookie
    {
        /**
         * Determines if the visitor is logged in or not.
         *
         * @return bool
         */
        public function isLoggedIn() : bool {
            return $this->offsetExists("id");
        }
    }
