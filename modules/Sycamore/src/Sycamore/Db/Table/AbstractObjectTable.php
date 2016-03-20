<?php
    namespace Sycamore\Db\Table;
    
    use Sycamore\Db\Row\AbstractRowInterface;
    use Sycamore\Db\Table\AbstractTable;
    use Sycamore\Db\Table\Exception\BadKeyException;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Object-specific abstract table gateway with extra functions specific to object elements.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractObjectTable extends AbstractTable
    {
        /**
         * Prepares the table with the DB adapter and local settings.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         * @param string $table The name of the table for this instance.
         * @param \Zend\Db\ResultSet\ResultSetInterface $row The row object to construct with results of queries.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager, $table, AbstractRowInterface& $row = NULL)
        {
            parent::__construct($serviceManager, $table, $row);
        }
        
        /**
         * Gets a row object by their ID.
         * 
         * @param $id int The ID of the object to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Sycamore\Db\Row\AbstractRow The fetched object.
         */
        public function getById($id, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("id", $id, $forceDbFetch);
        }
        
        /**
         * Gets the matching row objects by their IDs.
         * 
         * @param array $ids The IDs of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByIds($ids, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("id", $ids, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a creator of the given ID.
         * 
         * @param int $creatorId The ID of the creator of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByCreator($creatorId, $forceDbFetch = false)
        {
            return $this->getByKey($creatorId, "creatorId", $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a creator of the given IDs.
         * 
         * @param array $creatorIds The IDs of the creators of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByCreators($creatorIds, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("creatorId", $creatorIds, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a minimum creation time matching that given.
         * 
         * @param int $creationTimeMin The minimum creation time of objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByCreationTimeAfter($creationTimeMin, $forceDbFetch = false)
        {
            return $this->getByKeyGreaterThanOrEqualTo("creationTime", $creationTimeMin, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a maximum creation time matching that given.
         * 
         * @param int $creationTimeMax The maximum creation time of objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByCreationTimeBefore($creationTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyLessThanOrEqualTo("creationTime", $creationTimeMax, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a minimum and maximum creation time matching those given.
         * 
         * @param int $creationTimeMin The minimum creation time of objects to be retrieved.
         * @param int $creationTimeMax The maximum creation time of objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByCreationTimeRange($creationTimeMin, $creationTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("creationTime", $creationTimeMin, $creationTimeMax, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with most recent updator with the given ID.
         * 
         * @param int $updatorId The ID of the latest updator of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByUpdator($updatorId, $forceDbFetch = false)
        {
            return $this->getByKey($updatorId, "updatorId", $forceDbFetch);
        }
        
        /**
         * Get matching row objects with most recent updator with any of the given IDs.
         * 
         * @param array $updatorIds The IDs of the latest updators of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByUpdators($updatorIds, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("updatorId", $updatorIds, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a minimum update time matching that given.
         * 
         * @param int $updateTimeMin The minimum latest update time of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByUpdateTimeAfter($updateTimeMin, $forceDbFetch = false)
        {
            return $this->getByKeyGreaterThanOrEqualTo("updateTime", $updateTimeMin, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a maximum update time matching that given.
         * 
         * @param int $updateTimeMax The maximum latest update time of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByUpdateTimeBefore($updateTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyLessThanOrEqualTo("updateTime", $updateTimeMax, $forceDbFetch);
        }
        
        /**
         * Get matching row objects with a minimum and maximum update time matching those given.
         * 
         * @param int $updateTimeMin The minimum latest update time of the objects to be retrieved.
         * @param int $updateTimeMax The maximum latest update time of the objects to be retrieved.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched objects.
         */
        public function getByUpdateTimeRange($updateTimeMin, $updateTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("updateTime", $updateTimeMin, $updateTimeMax, $forceDbFetch);
        }
        
        /**
         * Deletes the entry with the given id.
         * 
         * @param int $id The ID of the object to be deleted.
         */
        public function deleteById($id)
        {
            $this->delete(["id" => (int) $id]);
        }
        
        /**
         * Saves the given row object, as an update if an ID is provided, as an insertion otherwise.
         * 
         * @param \Sycamore\Db\Row\AbstractRowInterface $row The row to be saved.
         * @param int $id The ID to save the row against.
         * 
         * @throws \Sycamore\Db\Table\Exception\BadKeyException If a given ID points to invalid row.
         */
        public function save(AbstractRowInterface $row, $id = 0)
        {
            if ($id == 0) {
                $this->insert($row->toArray());
            } else {
                if ($this->getById($id)) {
                    $this->update($row->toArray(), array("id" => $id));
                } else {
                    throw new BadKeyException("Record of ID $id does not exist.");
                }
            }
        }
    }