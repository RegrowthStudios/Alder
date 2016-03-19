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

    namespace Sycamore\Db\Row;
    
    /**
     * Interface setting out contract for all row objects.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    interface AbstractRowInterface
    {
        /**
         * Enters the data provided into the row instance, exchaning it for the old data.
         * 
         * @param array|\Traversable $data The data to be exchanged into the row instance.
         * 
         * @return array The old data of this row instance.
         * 
         * @throws \InvalidArgumentException if data provided is not an array.
         */
        public function exchangeArray($data);
        
        /**
         * Returns the data in this row instance in array form.
         * 
         * @return array The data in this row instance.
         */
        public function toArray();
    }
    