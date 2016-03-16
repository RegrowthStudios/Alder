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

    namespace Sycamore\Table;
    
    use Sycamore\Db\Row\Ban;
    use Sycamore\Db\Table\AbstractObjectTable;
    
    /**
     * Table representation class for bans.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Ban extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         */
        public function __construct(ServiceManager& $serviceManager)
        {
            parent::__construct($serviceManager, "bans", new Ban());
        }
        
        /**
         * Gets bans by the ID of the specified banned user.
         * 
         * @param int $id The ID of the banned user.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\Ban
         */
        public function getByBanned($id, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("bannedId", $id, $forceDbFetch);
        }
        
        /**
         * Gets bans by the IDs of the specified banned users.
         * 
         * @param array $ids The IDs of the banned users.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByBanneds($ids, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("bannedId", $ids, $forceDbFetch);
        }
        
        /**
         * Gets bans by their ban state.
         * 
         * @param int $state The state of the bans to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByState($state, $forceDbFetch = false)
        {
            return $this->getByKey("status", $state, $forceDbFetch);
        }
        
        /**
         * Gets bans of with an expiry time no sooner than that provided.
         * 
         * @param int $expiryTimeMin The minimum expiry time of bans to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByExpiryTimeMin($expiryTimeMin, $forceDbFetch = false)
        {
            return $this->getByKeyGreaterThanOrEqualTo("expiryTime", $expiryTimeMin, $forceDbFetch);
        }
        
        /**
         * Gets bans of with an expiry time no later than that provided.
         * 
         * @param int $expiryTimeMax The maximum expiry time of bans to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByExpiryTimeMax($expiryTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyLessThanOrEqualTo("expiryTime", $expiryTimeMax, $forceDbFetch);
        }
        
        /**
         * Gets bans of with an expiry time no sooner and no later than those limits provided.
         * 
         * @param int $expiryTimeMin The minimum expiry time of bans to fetch.
         * @param int $expiryTimeMax The maximum expiry time of bans to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByExpiryTimeRange($expiryTimeMin, $expiryTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("expiryTime", $expiryTimeMin, $expiryTimeMax, $forceDbFetch);
        }
    }
    
