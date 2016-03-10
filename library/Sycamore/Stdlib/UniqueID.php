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
        /**
         * Generates a partially random, unique ID. If strong is true, then a cryptographically secure random generator is used for a part of the ID.
         * With moreEntropy set to false, the ID will be 16 characters long, if moreEntropy is true then it will be 32 characters long. (Plus the length of the given prefix).
         * 
         * @param string $prefix The prefix to attach to the unique ID.
         * @param bool $strong Determines if a cryptographically secure random generator should be used for part of the ID.
         * @param bool $moreEntropy Determines if the ID should be longer and hence less likely to collide with another ID.
         * 
         * @return string The resulting unique ID.
         */
        public static function generate($prefix = "", $strong = false, $moreEntropy = false)
        {
            return uniqid($prefix . Rand::getString(($moreEntropy ? 9 : 3), Rand::ALPHANUMERIC, $strong), $moreEntropy);
        }
    }