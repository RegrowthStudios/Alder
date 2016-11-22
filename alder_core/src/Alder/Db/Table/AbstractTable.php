<?php
    
    namespace Alder\Db\Table;
    
    use Alder\DiContainer;
    use Alder\Stdlib\CacheUtils;
    
    use Zend\Db\Adapter\Adapter;
    use Zend\Db\Metadata\MetadataInterface;
    use Zend\Db\ResultSet\ResultSet;
    use Zend\Db\TableGateway\AbstractTableGateway;
    use Zend\Db\TableGateway\Feature\FeatureSet;
    use Zend\Db\TableGateway\Feature\MetadataFeature;
    use Zend\Db\TableGateway\Feature\RowGatewayFeature;
    use Zend\Db\RowGateway\RowGatewayInterface;
    use Zend\Db\Sql\Select;
    
    /**
     * Alder-specific implementation of Zend's abstract table gateway.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     * @abstract
     */
    abstract class AbstractTable extends AbstractTableGateway implements AbstractTableInterface
    {
        /**
         * Prepares the table with the DB adapter and local settings.
         *
         * @param string                                       $table   The name of the table for this instance.
         * @param array                                        $columns The columns of the table represented.
         * @param \Zend\Db\RowGateway\RowGatewayInterface|NULL $row     The row object to construct with the results of
         *                                                              queries.
         */
        protected function __construct(string $table, array $columns = null, RowGatewayInterface $row = null) {
            $container = DiContainer::get();
            
            // Prefix table.
            $this->table = $container->get("config")["alder"]["db"]["table_prefix"] . $table;
            
            // Acquire adapter from service container.
            $this->adapter = $container->get(Adapter::class);
            
            // Set columns if provided.
            if ($columns) {
                $this->columns = $columns;
            }
            
            // Create list of features to be used, preparing the metadata feature.
            $features = [new MetadataFeature($container->get(MetadataInterface::class))];
            
            // If a row prototype is provided add the row gateway feature.
            if ($row) {
                $features[] = new RowGatewayFeature($row);
            }
            
            // Set the feature set.
            $this->featureSet = new FeatureSet($features);
            
            // Initialise the table gateway. Sets up SQL object.
            $this->initialize();
        }
        
        /**
         * Fetches all rows matching the provided select parameters as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from the database.
         *
         * @param mixed  $select         The select object to make the selection with.
         * @param mixed  $cacheWhere     The parameters of the object(s) to be fetched.
         * @param string $cacheExtra     The extra details that identify the specific object(s) to be fetched.
         * @param array  $columnsToFetch The columns to fetch.
         * @param bool   $forceDbFetch   Whether to force a db fetch in this get.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of results from the selection.
         */
        protected function getBySelect($select, $cacheWhere, string $cacheExtra, array $columnsToFetch = null,
                                       bool $forceDbFetch = false) : ResultSet {
            // Generate the location in cache for the appropriate data.
            $cacheLocation = CacheUtils::generateCacheAddress($this->table . ":" . $cacheExtra, $cacheWhere,
                                                              $columnsToFetch);
            
            $container = DiContainer::get();
            
            // Grab the database cache.
            $dbCache = $container->get("AlderDbCache");
            
            // Fetch from cache if appropriate.
            $cacheFetchSuccess = false;
            $cachedResult = null;
            if (!$forceDbFetch && !$container->get("config")["alder"]["db"]["force_db_fetch"]) {
                $cachedResult = $dbCache->getItem($cacheLocation, $cacheFetchSuccess);
            }
            
            // Fetch from db if cache fails or if db fetch is forced.
            // Else set final result from fetched cache item.
            if (!$cacheFetchSuccess) {
                $select = $this->sql->select()->where($select)
                                    ->columns($columnsToFetch ?: $this->columns ?: Select::SQL_STAR);
                $result = $this->selectWith($select);
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
         * @param string $column         The fields to fetch by.
         * @param mixed  $value          The value of the provided key's column for rows that should be fetched.
         * @param array  $columnsToFetch The columns to fetch.
         * @param bool   $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByColumnWithValue(string $column, $value, array $columnsToFetch = null,
                                             bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect([$column => $value], $value, "get_by_$column", $columnsToFetch, $forceDbFetch);
        }
        
        /**
         * Fetches all rows matching the provided key values as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param array $columns        The fields to fetch by.
         * @param array $values         The values to match records against.
         * @param array $columnsToFetch The columns to fetch.
         * @param bool  $forceDbFetch
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByMultipleColumnsWithValues(array $columns, array $values, array $columnsToFetch = null,
                                                       bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect(array_combine($columns, $values), $values,
                                      "get_by" . array_reduce($columns, function ($carry, $item) {
                                          return $carry . "_" . $item;
                                      }, ""), $columnsToFetch, $forceDbFetch);
        }
        
        /**
         * Fetches a row matching the provided unique key value as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param string $column         The unique keyed field to fetch by.
         * @param mixed  $value          The value of the provided key's column for rows that should be fetched.
         * @param array  $columnsToFetch The columns to fetch.
         * @param bool   $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Alder\Db\Row\AbstractRowInterface|\ArrayObject|NULL The fetched item, NULL if no matches found.
         */
        public function getByUniqueKey(string $column, $value, array $columnsToFetch = null,
                                       bool $forceDbFetch = false) {
            return $this->getByColumnWithValue($column, $value, $columnsToFetch, $forceDbFetch)->current();
        }
        
        /**
         * Fetches the unique row matching the provided key values as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param array $columns        The fields of the composite key.
         * @param array $values         The values to match records against.
         * @param array $columnsToFetch The columns to fetch.
         * @param bool  $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Alder\Db\Row\AbstractRowInterface|\ArrayObject|NULL The fetched item, NULL if no matches found.
         */
        public function getByCompositeUniqueKey(array $columns, array $values, array $columnsToFetch = null,
                                                bool $forceDbFetch = false) {
            return $this->getByMultipleColumnsWithValues($columns, $values, $columnsToFetch, $forceDbFetch)->current();
        }
        
        /**
         * Fetches all rows between the provided key values as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param string           $column         The key to fetch by.
         * @param int|string|float $valueMin       The minimum value of range to fetch within.
         * @param int|string|float $valueMax       The maximum value of range to fetch within.
         * @param array            $columnsToFetch The columns to fetch.
         * @param bool             $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByColumnWithValueBetween(string $column, $valueMin, $valueMax, array $columnsToFetch = null,
                                                    bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect(function (Select $select) use ($column, $valueMin, $valueMax) {
                $select->where->between($column, $valueMin, $valueMax);
            }, strval($valueMin) . "_" . strval($valueMax), "get_between_$column", $columnsToFetch, $forceDbFetch);
        }
        
        /**
         * Fetches all rows greater than the provided key value as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param string           $column         The key to fetch by.
         * @param int|string|float $value          The minimum value of range to fetch within.
         * @param array            $columnsToFetch The columns to fetch.
         * @param bool             $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByColumnWithValueGreaterThanOrEqualTo(string $column, $value, array $columnsToFetch = null,
                                                                 bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect(function (Select $select) use ($column, $value) {
                $select->where->greaterThanOrEqualTo($column, $value);
            }, $value, "get_greater_than_or_equal_to_$column", $columnsToFetch, $forceDbFetch);
        }
        
        /**
         * Fetches all rows less than the provided key value as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param string           $column         The key to fetch by.
         * @param int|string|float $value          The maximum value of range to fetch within.
         * @param array            $columnsToFetch The columns to fetch.
         * @param bool             $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByColumnWithValueLessThanOrEqualTo(string $column, $value, array $columnsToFetch = null,
                                                              bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect(function (Select $select) use ($column, $value) {
                $select->where->lessThanOrEqualTo($column, $value);
            }, $value, "get_less_than_or_equal_to_$column", $columnsToFetch, $forceDbFetch);
        }
        
        /**
         * Fetches all rows that match one of the given values for the specified key as stored in cache,
         * if none are present in cache or $forceDbFetch is true, fetches from
         * the database.
         *
         * @param string $column          The key to fetch by.
         * @param array  $valueCollection The collection of values to check for in the provided key's column.
         * @param array  $columnsToFetch  The columns to fetch.
         * @param bool   $forceDbFetch    Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getByColumnWithValueInCollection(string $column, $valueCollection, array $columnsToFetch = null,
                                                         bool $forceDbFetch = false) : ResultSet {
            return $this->getBySelect(function (Select $select) use ($column, $valueCollection) {
                $select->where->in($column, $valueCollection);
            }, $valueCollection, "get_in_collection_$column", $columnsToFetch, $forceDbFetch);
        }
        
        // TODO(Matthew): Delete this? Not much point to it for the performance hit that comes with it.
        //        /*
        //         * ""
        //         * "Between"
        //         * "GreaterThanOrEqualTo"
        //         * "LessThanOrEqualTo"
        //         * "InCollection"
        //         */
        //        /**
        //         * Handles dynamic calls to retrieve entries based on one column's value.
        //         *
        //         * "getBy*"
        //         * "getBy*Between"
        //         * "getBy*GreaterThanOrEqualTo"
        //         * "getBy*LessThanOrEqualTo"
        //         * "getBy*InCollection"
        //         *
        //         * @param string $method The method called.
        //         * @param array $arguments The arguments passed to the method.
        //         *
        //         * @return mixed
        //         */
        //        public function __call($method, $arguments)
        //        {
        //            // Assert that the method is of the correct form and get the name of the desired column.
        //            $column = NULL;
        //            $result = [];
        //            $type = "";
        //            if (preg_match("/getBy([a-zA-Z]+)(Between)/", $method, $result)
        //                || preg_match("/getBy([a-zA-Z]+)(GreaterThanOrEqualTo)/", $method, $result)
        //                || preg_match("/getBy([a-zA-Z]+)(LessThanOrEqualTo)/", $method, $result)
        //                || preg_match("/getBy([a-zA-Z]+)(InCollection)/", $method, $result)) {
        //                $column = $result[1];
        //                $type= $result[2];
        //            }  else if (strpos($method, "getBy") === 0) {
        //                $column = substr($method, 5);
        //            } else {
        //                throw new \BadFunctionCallException("No function exists by the name: $method.");
        //            }
        //
        //            // Convert the column name from function name format to field name format.
        //            $column = implode("_", preg_split('/(?=[A-Z])/', $column));
        //
        //            // Check the target column exists.
        //            $success = false;
        //            foreach ($this->columns as $realColumn) {
        //                if ($realColumn === $column) {
        //                    $success = true;
        //                    break;
        //                }
        //            }
        //            if (!$success) {
        //                throw new \BadFunctionCallException("No column exists by the name: $column.");
        //            }
        //
        //            // Find which is the appropriate real function to call.
        //            // Then assert necessary arguments have been provided.
        //            // Finally, call real function and return results.
        //            switch ($type) {
        //                case "Between":
        //
        //                    break;
        //                case "GreaterThanOrEqualTo":
        //                    break;
        //                case "LessThanOrEqualTo":
        //                    break;
        //                case "InCollection":
        //                    break;
        //                default:
        //                    break;
        //            }
        //        }
        
        /**
         * Gets all entries of a table from cache if existent and if
         * $forceDbFetch is false, otherwise fetches from the database.
         *
         * @param array $columnsToFetch The columns to fetch.
         * @param bool  $forceDbFetch   Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched items.
         */
        public function getAll(array $columnsToFetch = null, bool $forceDbFetch = false) : ResultSet {
            // Generate the location in cache for fetch_all data.
            $cacheLocation = CacheUtils::generateCacheAddress($this->table . ":get_all", $columnsToFetch);
            
            $container = DiContainer::get();
            
            // Grab the database cache.
            $dbCache = $container->get("AlderDbCache");
            
            // Fetch from cache if appropriate.
            $cacheFetchSuccess = false;
            $cachedResult = null;
            if (!$forceDbFetch && !$container->get("config")["alder"]["db"]["force_db_fetch"]) {
                $cachedResult = $dbCache->getItem($cacheLocation, $cacheFetchSuccess);
            }
            
            // Fetch from db if cache fails or if db fetch is forced.
            // Else set final result from fetched cache item.
            if (!$cacheFetchSuccess) {
                $select = $this->sql->select()->columns($columnsToFetch ?: $this->columns ?: Select::SQL_STAR);
                $result = $this->selectWith($select);
                $dbCache->setItem($cacheLocation, $result);
            } else {
                $result = $cachedResult;
            }
            
            // Return the resulting data.
            return $result;
        }
    }
