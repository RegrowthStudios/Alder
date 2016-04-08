<?php
    namespace Sycamore\Stdlib;

    use Zend\Stdlib\ArrayUtils as ZendArrayUtils;
    
    /**
     * Holds functions for checking the existence of keys and values in arrays.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ArrayUtils extends ZendArrayUtils
    {
        /**
         * Recursively checks if a value exists in an array.
         *
         * @var string The value to check for.
         * @var array The array to search through.
         * @var bool Should value type match?
         *
         * @return bool If the item to search for exists in the given array.
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
         * @var string The key to check for.
         * @var array The array to search through.
         *
         * @return bool True if the array key to search for exists.
         */
        public static function arrayKeyExistsRecursive($key, $array)
        {
            foreach ($array as $k => $v) {
                if (($k === $key) || (is_array($v) && static::arrayKeyExistsRecursive($key, $v))) {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * Recursively asorts the given array.
         * 
         * @param array $array The array to be sorted.
         * 
         * @return bool True on success, false on failure.
         */
        public static function recursiveAsort(array& $array)
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
         * @param array $array The array to be sorted.
         * 
         * @return bool True on success, false on failure.
         */
        public static function recursiveKsort(array& $array)
        {
            foreach ($array as & $value) {
                if (is_array($value)) {
                    static::recursiveKsort($value);
                }
            }
            return ksort($array);
        }
        
        /**
         * Checks data is array-like, and returns as an array-accessible type.
         * 
         * @param mixed $data The data to be validated.
         * @param string $class The class of the caller for constructing exception message.
         * @param bool $arrayOnly Whether the data should be able to be transformed into an array.
         * 
         * @return array|\ArrayAccess The resulting data cast into an array accessible form.
         * 
         * @throws \InvalidArgumentException If the data was in a form not castable into an array accessible form.
         */
        public static function validateArrayLike($data, $class = NULL, $arrayOnly = false)
        {
            if (is_array($data)) {
                return $data;
            }
            
            if ($data instanceof \Traversable) {
                $data = ArrayUtils::iteratorToArray($data);
                return $data;
            }
            
            
            if ($arrayOnly) {
                if (is_null($class)) {
                    $class = get_called_class();
                }
                throw new \InvalidArgumentException($class . "expected an array or \Traversable object.");
            }
            
            if (!$data instanceof \ArrayAccess) {
                if (is_null($class)) {
                    $class = get_called_class();
                }
                throw new \InvalidArgumentException($class . " expected an array, or object that implemens \Traversable or \ArrayAccess.");
            }
            
            return $data;
        }
        
        // TODO(Matthew): Create and use custom array_diff that handles objects better.
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