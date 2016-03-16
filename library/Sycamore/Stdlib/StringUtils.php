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

    namespace Sycamore\Stdlib;
    
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
                        $string .= static::convertToString($val) . "_";;;
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
            return str_replace(array("\\", "/"), "_", $string);
        }
        
        /**
         * Determines if one string ends with another string.
         * 
         * @param string $haystack The string to evaluate the ending of.
         * @param string $needle The string to search for at the end of $haystack.
         * @return boolean True if $haystack ends with $needle, false otherwise.
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
        