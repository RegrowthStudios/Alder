<?php
    
    namespace Alder\Cache;
    
    use Interop\Container\ContainerInterface;
    
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
         * Creates a database cache service in the container.
         *
         * @param \Interop\Container\ContainerInterface $container The container for the application instance.
         *
         * @return \Zend\Cache\Storage\StorageInterface The caching object for database entries.
         */
        public function __invoke(ContainerInterface $container) : StorageInterface {
            $cacheConfig = $container->get("config")["alder"]["db"]["cache"];
            
            $cache = StorageFactory::factory(["adapter" => $cacheConfig["adapter"],
                                              "options" => ["ttl" => $cacheConfig["time_to_live"],],
                                              "namespace" => $cacheConfig["namespace"]]);
            $pluginsConfig = $cacheConfig["plugins"];
            $pluginOptions = new Plugin\PluginOptions(["ClearingFactor" => $pluginsConfig["clear_expired"]["clearing_factor"],
                                                       "ExitOnAbort" => $pluginsConfig["ignore_user_abort"]["exit_on_abort"],
                                                       "OptimizingFactor" => $pluginsConfig["optimise"]["optimising_factor"],]);
            
            $clearExpired = new Plugin\ClearExpiredByFactor();
            $clearExpired->setOptions($pluginOptions);
            $cache->addPlugin($clearExpired);
            
            $ignoreUserAbort = new Plugin\IgnoreUserAbort();
            $ignoreUserAbort->setOptions($pluginOptions);
            $cache->addPlugin($ignoreUserAbort);
            
            $optimise = new Plugin\OptimizeByFactor();
            $optimise->setOptions($pluginOptions);
            $cache->addPlugin($optimise);
            
            $serialiser = new Plugin\Serializer();
            $cache->addPlugin($serialiser);
            
            return $cache;
        }
    }
