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

    namespace Sycamore\Stdlib\ArrayUtils;
    
    use Sycamore\Stdlib\ArrayUtils;
    
    /**
     * Provides functionality for ensuring data sets are array-like.
     */
    class ArrayLikeValidation
    {
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
        public static function validateData($data, $class, $arrayOnly = false)
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
    }