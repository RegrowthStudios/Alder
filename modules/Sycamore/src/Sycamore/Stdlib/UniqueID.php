<?php
    namespace Sycamore\Stdlib;
    
    use Sycamore\Stdlib\Rand;
    
    /**
     * Provides a utility function gor generating unique IDs with extra entropy than solely uniqid().
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016 Matthew Marshall
     * @since 0.1.0
     */
    class UniqueID
    {
        const LONG = 1;
        const MEDIUM = 2;
        const SHORT = 3;
        
        /**
         * Generates a partially random, unique ID. If strong is true, then a cryptographically secure random generator is used for a part of the ID.
         * With the SHORT flag, the ID will be 16 characters long, the MEDIUM flag yields a 19 character ID and the LONG flag a 32 character ID (Plus the length of the given prefix).
         * 
         * @param string $prefix The prefix to attach to the unique ID.
         * @param bool $strong Determines if a cryptographically secure random generator should be used for part of the ID.
         * @param bool $length Determines what length the ID should be.
         * 
         * @return string The resulting unique ID.
         */
        public static function generate($prefix = "", $strong = false, $length = self::MEDIUM)
        {
            $randLength = 6;
            $moreEntropy = false;
            if ($length === self::LONG) {
                $randLength = 9;
                $moreEntropy = true;
            } else if ($length === self::SHORT) {
                $randLength = 3;
            } else if (!($length === self::MEDIUM)) {
                throw new \InvalidArgumentException("The flags provided were invalid!");
            }
            return uniqid($prefix . Rand::getString($randLength, Rand::ALPHANUMERIC, $strong), $moreEntropy);
        }
    }