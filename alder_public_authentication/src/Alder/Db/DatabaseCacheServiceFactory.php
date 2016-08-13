<?php
    
    namespace Alder\Db;
    
    use Alder\Container;
    
    use Zend\Cache\StorageFactory;
    use Zend\Cache\Storage\Plugin;
    
    /**
     * Factory for creating database cache services.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class DatabaseCacheServiceFactory
    {
        // TODO(Matthew): Allow greater configurability for different cache services 
        //                per database, or even table.
        /**
         * Creates a database cache service in the container.
         */
        public function create() {
            $container = Container::get();
            
            $cacheConfig = $container->get("config")["alder"]["db"]["cache"];
            
            $cache = StorageFactory::factory([
                "adapter" => $cacheConfig["adapter"],
                "options" => [
                    "ttl" => $cacheConfig["timeToLive"],
                ],
                "namespace" => $cacheConfig["namespace"]
            ]);
            $pluginsConfig = $cacheConfig["plugins"];
            $pluginOptions = new Plugin\PluginOptions([
                "ClearingFactor" => $pluginsConfig["clearExpired"]["clearingFactor"],
                "ExitOnAbort" => $pluginsConfig["ignoreUserAbort"]["exitOnAbort"],
                "OptimizingFactor" => $pluginsConfig["optimise"]["optimisingFactor"],
            ]);
            
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
            
            $container->setService("AlderDbCache", $cache);
        }
    }
