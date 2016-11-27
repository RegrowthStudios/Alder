<?php
    
    namespace Alder\PublicAuthentication\User;
    
    use Alder\DiContainer;
    
    /**
     * Provides utility functions for hashing, verifying and
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Password
    {
        /**
         * Hashes the given password.
         *
         * @param string $password The password to be hashed.
         *
         * @return bool|string Hashed password, or false on failure.
         */
        public static function hash(string $password) {
            return password_hash($password, PASSWORD_DEFAULT, ["cost" => DiContainer::get()
                                                                                  ->get("config")["alder"]["public_authentication"]["password"]["hashing_strength"]]);
        }
        
        /**
         * Verify a password is valid.
         *
         * @param string $password The password to be validated.
         * @param string $hash     The hash for the password to be validated against.
         *
         * @return bool True if password is valid, false otherwise.
         */
        public static function verify(string $password, string $hash) : bool {
            return password_verify($password, $hash);
        }
        
        /**
         * Determines if a password hash needs rehashing.
         *
         * @param string $hash The password hash to be checked.
         *
         * @return bool True if password needs rehashing, false otherwise.
         */
        public static function needsRehash(string $hash) : bool {
            return password_needs_rehash($hash, PASSWORD_DEFAULT, ["cost" => DiContainer::get()
                                                                                      ->get("config")["alder"]["public_authentication"]["password"]["hashing_strength"]]);
        }
    }
