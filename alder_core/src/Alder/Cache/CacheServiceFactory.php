<?php
    
    namespace Alder\Cache;
    
    use Alder\DiContainer;
    
    use Zend\Cache\Storage\StorageInterface;
    use Zend\Cache\StorageFactory;
    
    /**
     * Factory for creating cache services.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class CacheServiceFactory
    {
        /**
         * Creates a cache service for the specified purpose, module and library.
         *
         * @param string $module The module for which to create the service, defaults to the global service of the given library.
         * @param string $library The library for which to create the service, defaults to "alder".
         *
         * @return \Zend\Cache\Storage\StorageInterface The caching object for database entries.
         */
        public static function create(string $purpose, string $module = null, string $library = null) : StorageInterface {
            $container = DiContainer::get();
            
            if (!$module) {
                $cacheConfig = $container->get("config")[($library ?: "alder")][$purpose]["cache"];
            } else {
                $cacheConfig = $container->get("config")[($library ?: "alder")][$module][$purpose]["cache"];
            }
            
            return StorageFactory::factory($cacheConfig);
        }
    }
