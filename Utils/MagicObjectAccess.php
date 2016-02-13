<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
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

    namespace Sycamore\Utils;
    
    class MagicObjectAccess
    {
        public function __set($key, $value)
        {
            $func = "set" . ucfirst($key);
            if (method_exists($this, $func)) {
                $this->$func($value);
            } else {
                throw new \RuntimeException("No property, $key, exists that is settable.");
            }
        }
        
        public function &__get($key)
        {
            $func = "get" . ucfirst($key);
            if (method_exists($this, $func)) {
                return $this->$func();
            } else {
                throw new \RuntimeException("No property, $key, exists that is gettable.");
            }
        }          
    }
    