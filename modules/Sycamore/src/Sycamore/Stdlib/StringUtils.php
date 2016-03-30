<?php
    namespace Sycamore\Stdlib;
    
    /**
     * Provides utility functions for operating on strings.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class StringUtils
    {
        /**
         * Converts various possible datatypes of data provided to a string.
         * 
         * @param mixed $data The data to be converted to string.
         * 
         * @return string The resulting string from the data given.
         */
        public static function convertToString($data)
        {
            $string = "_";
            switch (gettype($data))
            {
                case "string":
                    $string .= preg_replace("#[\\\/.]+#", "_", $data);
                    break;
                case "integer":
                case "double":
                    $string .= (string) $data;
                    break;
                case "boolean":
                    $string .= ($data ? "true" : "false");
                    break;
                case "array":
                    asort($data);
                    foreach ($data as $key => $val) 
                    {
                        if (is_string($key)) {
                            $string .= $key . "_";
                        }
                        $string .= static::convertToString($val) . "_";
                    }
                    break;
                case "object":
                    $string = serialize($data);
                    break;
                case "NULL":
                    $string = "";
                    break;
                default:
                    $string .= preg_replace("#[\\\/.]+#", "_", strval($data));
                    break;
            }
            return str_replace(["\\", "/"], "_", $string);
        }
        
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
        