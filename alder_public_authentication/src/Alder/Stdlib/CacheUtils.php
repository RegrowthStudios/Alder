<?php

    namespace Alder\Stdlib;
    
    use Alder\Stdlib\StringUtils;
    
    /**
     * Provides utility functions for caching.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class CacheUtils
    {
        /**
         * Generates the name for the given cache entry.
         * 
         * @param string $location The "general" location of the desired cache item.
         * @param mixed $where The specific paramters that uniquely identify the desired cache item.
         * 
         * @return string The generated cache address.
         * 
         * @throws \InvalidArgumentException If $location is not a string.
         */
        public static function generateCacheAddress($location, $where)
        {
            if (!is_string($location)) {
                throw new \InvalidArgumentException("The location provided must be a string.");
            }
            
            $cacheName = $location;
            
            $cacheName .= StringUtils::convertToString($where);
            
            return $cacheName;
        }
    }