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
    
    use Sycamore\Stdlib\Rand;
    
    /**
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016 Matthew Marshall
     */
    class UniqueID
    {
        const LONG = 1;
        const MEDIUM = 2;
        const SHORT = 4;
        
        /**
         * Generates a partially random, unique ID. If strong is true, then a cryptographically secure random generator is used for a part of the ID.
         * With the SHORT flag, the ID will be 16 characters long, the MEDIUM flag yields a 19 character ID and the LONG flag a 32 character ID (Plus the length of the given prefix).
         * 
         * @param string $prefix The prefix to attach to the unique ID.
         * @param bool $strong Determines if a cryptographically secure random generator should be used for part of the ID.
         * @param bool $moreEntropy Determines if the ID should be longer and hence less likely to collide with another ID.
         * 
         * @return string The resulting unique ID.
         */
        public static function generate($prefix = "", $strong = false, $flags = self::MEDIUM)
        {
            $randLength = 6;
            $moreEntropy = false;
            if (self::LONG & $flags) {
                $randLength = 9;
                $moreEntropy = true;
            } else if (self::SHORT & $flags) {
                $randLength = 3;
            } else if (!(self::MEDIUM & $flags)) {
                throw new \InvalidArgumentException("The flags provided were invalid!");
            }
            return uniqid($prefix . Rand::getString($randLength, Rand::ALPHANUMERIC, $strong), $moreEntropy);
        }
    }