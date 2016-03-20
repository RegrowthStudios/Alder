<?php
    namespace Sycamore\Db\Table;
    
    use Sycamore\Db\Row\Ban as BanRow;
    use Sycamore\Db\Table\AbstractObjectTable;

    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Table representation class for bans.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Ban extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager of this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            parent::__construct($serviceManager, "bans", new BanRow());
        }
        
        /**
         * Gets bans by the ID of the specified banned user.
         * 
         * @param int $id The ID of the banned user.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\Ban The fetched ban.
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
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched bans.
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
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched bans.
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
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched bans.
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
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched bans.
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
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched bans.
         */
        public function getByExpiryTimeRange($expiryTimeMin, $expiryTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("expiryTime", $expiryTimeMin, $expiryTimeMax, $forceDbFetch);
        }
    }
    
