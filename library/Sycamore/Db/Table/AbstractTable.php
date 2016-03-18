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

    namespace Sycamore\Db\Table;
    
    use Sycamore\Cache\CacheUtils;
    use Sycamore\Db\Row\AbstractRowInterface;
    
    use Zend\Db\TableGateway\AbstractTableGateway;
    use Zend\Db\TableGateway\Feature\FeatureSet;
    use Zend\Db\TableGateway\Feature\GlobalAdapterFeature;
    use Zend\Db\ResultSet\ResultSet;
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Sycamore-specific implementation of Zend's abstract table gateway.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractTable extends AbstractTableGateway
    {
        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceManager
         */
        protected $serviceManager;
        
        /**
         * Prepares the table with the DB adapter and local settings.
         * 
         * @param \Zend\ServiceManager\ServiceManager $serviceManager The service manager for this application instance.
         * @param string $table The name of the table for this instance.
         * @param \Zend\Db\ResultSet\ResultSetInterface $row The row object to construct with results of queries.
         */
        public function __construct(ServiceManager& $serviceManager, $table, AbstractRowInterface& $row = NULL)
        {
            // Set service manager for this table gateway instance.
            $this->serviceManager = $serviceManager;
            
            // Prefix table.
            $config = $this->serviceManager->get("Config")["Sycamore"];
            $this->table = $config["db"]["tablePrefix"] . $table;
            
            // Add global adapter feature.
            $this->featureSet = new FeatureSet();
            $this->featureSet->addFeature(new GlobalAdapterFeature());
            
            // Set the result set prototype as provided as as generic if none provided.
            $this->resultSetPrototype = new ResultSet();
            if ($row) {
                $this->resultSetPrototype->setArrayObjectPrototype($row);
            }
            
            // Initialise the table gateway. Sets up SQL object 
            $this->initialize();
        }
        
        /**
         * Fetches all rows matching the provided select parameters as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from the database.
         * 
         * @param mixed $select The select object to make the selection with.
         * @param mixed $cacheWhere The parameters of the object(s) to be fetched.
         * @param string $cacheExtra The extra details that identify the specific object(s) to be fetched.
         * @param bool $forceDbFetch Whether to force a db fetch in this get.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of results from the selection.
         */
        protected function getBySelect($select, $cacheWhere, $cacheExtra, $forceDbFetch = false)
        {
            // Generate the location in cache for the appropriate data.
            $cacheLocation = CacheUtils::generateCacheAddress($this->table . $cacheExtra, $cacheWhere);
            
            // Grab the database cache.
            $dbCache = $this->serviceManager->get("DbCache");            
            
            // Fetch from cache if appropriate.
            $cacheFetchSuccess = false;
            if (!$forceDbFetch && !$this->serviceManager->get("Config")["Sycamore"]["db"]["forceDbFetch"]) {
                $cachedResult = $dbCache->getItem($cacheLocation, $cacheFetchSuccess);
            }
            
            // Fetch from db if cache fails or if db fetch is forced.
            // Else set final result from fetched cache item.
            if (!$cacheFetchSuccess) {
                $result = $this->select($select);
                $dbCache->setItem($cacheLocation, $result);
            } else {
                $result = $cachedResult;
            }
            
            // Return the resulting data.
            return $result;
        }
        
        /**
         * Fetches all rows matching the provided key value as stored in cache, 
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param mixed $value The value of the provided key's column for rows that should be fetched.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        protected function getByKey($key, $value, $forceDbFetch = false)
        {
            return $this->getBySelect(
                [$key => $value],
                $value,
                "get_by_$key",
                $forceDbFetch
            );
        }
        
        /**
         * Fetches a row matching the provided unique key value as stored in cache, 
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param mixed $value The value of the provided key's column for rows that should be fetched.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return array|\ArrayObject|NULL Array if no prototype is specified for this table, an object implementing \ArrayObject if a prototype was specified.
         * NULL if no matches were found for the fetch parameters.
         */
        protected function getByUniqueKey($key, $value, $forceDbFetch = false)
        {
            return $this->getByKey($key, $value, $forceDbFetch)->current();
        }
        
        /**
         * Fetches all rows between the provided key values as stored in cache, 
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param int|string|float $valueMin The minimum value of range to fetch within.
         * @param int|string|float $valueMax The maximum value of range to fetch within.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        protected function getByKeyBetween($key, $valueMin, $valueMax, $forceDbFetch = false)
        {
            return $this->getBySelect(
                function (Select $select) use ($key, $valueMin, $valueMax) {
                    $select->where->between($key, $valueMin, $valueMax);
                },
                strval($valueMin) . strval($valueMax),
                "get_between_$key",
                $forceDbFetch
            );
        }
        
        /**
         * Fetches all rows greater than the provided key value as stored in cache, 
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param int|string|float $value The minimum value of range to fetch within.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        protected function getByKeyGreaterThanOrEqualTo($key, $value, $forceDbFetch = false)
        {
            return $this->getBySelect(
                function (Select $select) use ($key, $value) {
                    $select->where->greaterThanOrEqualTo($key, $value);
                },
                $value,
                "get_greater_than_or_equal_to_$key",
                $forceDbFetch
            );
        }
        
        /**
         * Fetches all rows less than the provided key value as stored in cache, 
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param int|string|float $value The maximum value of range to fetch within.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        protected function getByKeyLessThanOrEqualTo($key, $value, $forceDbFetch = false)
        {
            return $this->getBySelect(
                function (Select $select) use ($key, $value) {
                    $select->where->lessThanOrEqualTo($key, $value);
                },
                $value,
                "get_less_than_or_equal_to_$key",
                $forceDbFetch
            );
        }
        
        /**
         * Fetches all rows that match one of the given values for the specified key as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from 
         * the database.
         * 
         * @param string $key The key to fetch by.
         * @param array $valueCollection The collection of values to check for in the provided key's column.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        protected function getByKeyInCollection($key, $valueCollection, $forceDbFetch = false)
        {
            return $this->getBySelect(
                function (Select $select) use ($key, $valueCollection) {
                    $select->where->in($key, $valueCollection);
                },
                $valueCollection,
                "get_in_collection_$key",
                $forceDbFetch
            );
        }
        
        /**
         * Gets all entries of a table from cache if existent and if 
         * $forceDbFetch is false, otherwise fetches from the database.
         * 
         * @param bool Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function fetchAll($forceDbFetch = false)
        {
            // Generate the location in cache for fetch_all data.
            $cacheLocation = CacheUtils::generateCacheAddress($this->table . "fetch_all", NULL);
            
            // Grab the database cache.
            $dbCache = $this->serviceManager->get("DbCache");            
            
            // Fetch from cache if appropriate.
            $cacheFetchSuccess = false;
            if (!$forceDbFetch && !$this->serviceManager->get("Config")["Sycamore"]["db"]["forceDbFetch"]) {
                $cachedResult = $dbCache->getItem($cacheLocation, $cacheFetchSuccess);
            }
            
            // Fetch from db if cache fails or if db fetch is forced.
            // Else set final result from fetched cache item.
            if (!$cacheFetchSuccess) {
                $result = $this->select();
                $dbCache->setItem($cacheLocation, $result);
            } else {
                $result = $cachedResult;
            }
            
            // Return the resulting data.
            return $result;
        }
        
        /**
         * Updates rows matching the given identifiers.
         * 
         * @param \Sycamore\Row\Row $row The row of data to update with.
         * @param Where|\Closure|string|array $identifiers The identifiers for rows that should be updated.
         * 
         * @return int The number of rows affected by the update execution.
         */
        public function update(AbstractRow $row, $identifiers = NULL) {
            return parent::update($row->toArray(), $identifiers);
        }
        
        /**
         * Inserts a new row to the table.
         * 
         * @param \Sycamore\Db\Row\AbstractRow $row The row to be inserted.
         * 
         * @return int
         */
        public function insert(AbstractRow $row)
        {
            return parent::insert($row->toArray());
        }
    }
