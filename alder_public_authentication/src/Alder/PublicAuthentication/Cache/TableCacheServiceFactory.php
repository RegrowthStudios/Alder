<?php
    
    namespace Alder\PublicAuthentication\Cache;
    
    use Alder\Db\TableCache;
    
    use Interop\Container\ContainerInterface;
    
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
         * @param \Interop\Container\ContainerInterface $container The container for the application instance.
         *
         * @return \Alder\Db\TableCache The table cache.
         */
        public function __invoke(ContainerInterface $container) : TableCache {
            return new TableCache("Alder\\PublicAuthentication\\Db\\Table\\");
        }
    }
