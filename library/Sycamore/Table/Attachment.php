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


    namespace Sycamore\Table;
    
    use Sycamore\Row\Attachment;
    use Sycamore\Table\Table;
    
    class Attachment extends Table
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         */
        public function __construct()
        {
            parent::__construct("attachments", new Attachment());
        }
        
        /**
         * Gets an attachment by its ID.
         * 
         * @var int
         * @var boolean
         *
         * @return \Sycamore\Row\Attachment
         */
        public function getById($id, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("id", $id, $forceDbFetch);
        }
        
        /**
         * Gets the matching attachments by their IDs.
         * 
         * @param array $ids
         * @param bool $forceDbFetch
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByIds($ids, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("id", $ids, $forceDbFetch);
        }
    }
        