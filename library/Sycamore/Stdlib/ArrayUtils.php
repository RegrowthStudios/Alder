<?php

/* 
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
 */

    namespace Sycamore\Stdlib;

    use Zend\Stdlib\ArrayUtils as ZendArrayUtils;
    
    /**
     * Holds functions for checking the existence of keys and values in arrays.
     */
    class ArrayUtils extends ZendArrayUtils
    {
        /**
         * Recursively checks if a value exists in an array.
         *
         * @var string - The value to check for.
         * @var array - The array to search through.
         * @var boolean - Should value type match?
         *
         * @return boolean
         */
        public static function inArrayRecursive($needle, $haystack, $strict = false) 
        {
            foreach ($haystack as $item) {
                if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && static::inArrayRecursive($needle, $item, $strict))) {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * Recursively checks if a key exists in an array.
         *
         * @var string - The key to check for.
         * @var array - The array to search through.
         *
         * @return boolean
         */
        public static function arrayKeyExistsRecursive($key, $array)
        {
            foreach ($array as $k => $v) {
                if (($k == $key) || (is_array($v) && static::arrayKeyExistsRecursive($key, $v))) {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * Recursively asorts the given array.
         * 
         * @uses asort
         * 
         * @param array $array The array to be sorted.
         * 
         * @return bool True on success, False on failure.
         */
        public static function recursiveAsort(& $array)
        {
            foreach ($array as & $value) {
                if (is_array($value)) {
                    static::recursiveAsort($value);
                }
            }
            return asort($array);
        }
        
        /**
         * Recursively ksorts the given array.
         * 
         * @uses ksort
         * 
         * @param array $array The array to be sorted.
         * 
         * @return bool True on success, False on failure.
         */
        public static function recursiveKsort(& $array)
        {
            foreach ($array as & $value) {
                if (is_array($value)) {
                    static::recursiveAsort($value);
                }
            }
            return ksort($array);
        }
        
        /**
         * Checks data is array-like, and returns as an array-accessible type.
         * 
         * @param mixed $data
         * @param string $class
         * @param bool $arrayOnly
         * 
         * @return array|\ArrayAccess
         * @throws \InvalidArgumentException
         */
        public static function validateArrayLike($data, $class, $arrayOnly = false)
        {
            if (is_array($data)) {
                return $data;
            }
            
            if ($data instanceof \Traversable) {
                $data = ArrayUtils::iteratorToArray($data);
                return $data;
            }
            
            if ($arrayOnly) {
                throw new \InvalidArgumentException($class . "expected an array or \Traversable object.");
            }
            
            if (!$data instanceof \ArrayAccess) {
                throw new \InvalidArgumentException($class . " expected an array, or object that implemens \Traversable or \ArrayAccess.");
            }
            
            return $data;
        }
        
        /**
         * Performs an XOR operation on two arrays.
         * 
         * @param array $array1 The first array to act on.
         * @param array $array2 The second array to act on.
         * 
         * @return array The resulting array of XOR operation.
         */
        public static function xorArrays($array1, $array2)
        {
            return array_merge(
                array_diff($array1, $array2),
                array_diff($array2, $array1)
            );
        }
    }