<?php
    
    namespace Alder\Cache;
    
    use Alder\DiContainer;
    
    use Zend\Cache\Storage\StorageInterface;
    use Zend\Cache\StorageFactory;
    use Zend\Cache\Storage\Plugin;
    
    /**
     * Factory for creating database cache services.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class DatabaseCacheServiceFactory
    {
        // TODO(Matthew): Allow greater configurability for different cache services per database, or even table.
        /**
         * Creates a database cache service.
         *
         * @param string $module The module for which to create the service, defaults to the global service of the given library.
         * @param string $library The library for which to create the service, defaults to "alder".
         *
         * @return \Zend\Cache\Storage\StorageInterface The caching object for database entries.
         */
        public static function create(string $module = null, string $library = null) : StorageInterface {
            $container = DiContainer::get();
        
            if (!$module) {
                $cacheConfig = $container->get("config")[($library ?: "alder")]["db"]["cache"];
            } else {
                $cacheConfig = $container->get("config")[($library ?: "alder")][$module]["db"]["cache"];
            }
            
            $cache = StorageFactory::factory(["adapter" => $cacheConfig["adapter"],
                                              "options" => ["ttl" => $cacheConfig["time_to_live"],],
                                              "namespace" => $cacheConfig["namespace"]]);
            
            $pluginsConfig = $cacheConfig["plugins"];
            $pluginOptions = new Plugin\PluginOptions();
            
            if (isset($pluginsConfig["clear_expired"])) {
                $pluginOptions->setClearingFactor($pluginsConfig["clear_expired"]["clearing_factor"]);
                
                $clearExpired = new Plugin\ClearExpiredByFactor();
                $clearExpired->setOptions($pluginOptions);
                $cache->addPlugin($clearExpired);
            }
            
            if (isset($pluginsConfig["ignore_user_abort"])) {
                $pluginOptions->setExitOnAbort($pluginsConfig["ignore_user_abort"]["exit_on_abort"]);
    
                $ignoreUserAbort = new Plugin\IgnoreUserAbort();
                $ignoreUserAbort->setOptions($pluginOptions);
                $cache->addPlugin($ignoreUserAbort);
            }
            
            if (isset($pluginConfig["optimise"])) {
                $pluginOptions->setOptimizingFactor($pluginsConfig["optimise"]["optimising_factor"]);
    
                $optimise = new Plugin\OptimizeByFactor();
                $optimise->setOptions($pluginOptions);
                $cache->addPlugin($optimise);
            }
            
            $serialiser = new Plugin\Serializer();
            $cache->addPlugin($serialiser);
            
            return $cache;
        }
    }
