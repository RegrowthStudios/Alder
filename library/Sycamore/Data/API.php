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

    namespace Sycamore\Data;

    use Zend\Json\Json;
    
    /**
     * Simple wrapper of encode and decode methodologies for JSONifiable data.
     */
    class API
    {
        /**
         * Encodes the given API data appropriately, returning a string of the encoded form.
         * 
         * @param mixed $data
         * 
         * @return string
         */
        public static function encode($data)
        {
            return Json::encode($data);
        }
        
        /**
         * Decodes the given API data appropriately, returning the decode results. Objects are decoded as associative arrays.
         * 
         * @param string $data
         * 
         * @return mixed
         */
        public static function decode($data)
        {
            return Json::decode($data, Json::TYPE_ARRAY);
        }
    }
    