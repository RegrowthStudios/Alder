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

    namespace Sycamore\Serialiser;
    
    use Zend\Serializer\Adapter\AdapterOptions;
    use Zend\Serializer\Serializer;
    
    abstract class AbstractSerialiser
    {
        /**
         * Serialiser adapter for serialising data.
         * 
         * @var \Zend\Serializer\Adapter\AbstractAdapter
         */
        protected static $serialiser;
        
        /**
         * Type of this instance's serialiser adapter.
         * 
         * @var string
         */
        protected static $serialiserType = "";
        /**
         * Options for a given instance's serialiser adapter.
         *
         * @var array
         */
        protected static $options = [];
        
        /**
         * Constructs the instance's serialiser if not already constructed and returns it.
         * 
         * @return \Zend\Serializer\Adapter\AbstractAdapter
         */
        protected static function getSerialiser()
        {
            if (!isset(static::$serialiser)) {
                static::$serialiser = Serializer::factory(static::$serialiserType);
                if (!empty(static::$options)) {
                    static::$serialiser->setOptions(new AdapterOptions(static::$options));
                }
            }
            return static::$serialiser;
        }
        
        /**
         * Encodes the given data appropriately, returning a string of the encoded form.
         * 
         * @param mixed $data
         * 
         * @return string
         */
        public static function encode($data)
        {
            return static::getSerialiser()->serialize($data);
        }
        
        /**
         * Decodes the given data appropriately, returning the decode results. Objects are decoded as associative arrays.
         * 
         * @param string $data
         * 
         * @return mixed
         */
        public static function decode($data)
        {
            return static::getSerialiser()->unserialize($data);
        }
    }
