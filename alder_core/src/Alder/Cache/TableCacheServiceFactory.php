<?php
    
    namespace Alder\Cache;
    
    use Alder\Db\TableCache;
    
    /**
     * Factory for creating table cache services.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class TableCacheServiceFactory
    {
        /**
         * Creates a table cache service in the container.
         *
         * @param string $namespace The namespace in which the tables of this cache lie.
         *
         * @return \Alder\Db\TableCache The table cache.
         */
        public static function create(string $namespace) : TableCache {
            return new TableCache($namespace);
        }
    }
