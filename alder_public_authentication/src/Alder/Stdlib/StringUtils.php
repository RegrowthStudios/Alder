<?php
    namespace Alder\Stdlib;
    
    /**
     * Provides utility functions for operating on strings.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class StringUtils
    {
        /**
         * Determines if one string ends with another string.
         * 
         * @param string $haystack The string to evaluate the ending of.
         * @param string $needle The string to search for at the end of $haystack.
         * 
         * @return bool True if $haystack ends with $needle, false otherwise.
         */
        public static function endsWith($haystack, $needle)
        {
            $haystackLength = strlen($haystack);
            $needleLength = strlen($needle);
            if ($haystackLength < $needleLength) {
                return false;
            }
            return substr_compare($haystack, $needle, $haystackLength - $needleLength, $needleLength) === 0;
        }
    }
