<?php
    
    namespace Alder\Cache;
    
    use Alder\DiContainer;
        
    use Zend\Cache\Storage\StorageInterface;
    use Zend\Cache\StorageFactory;
    use Zend\Cache\Storage\Plugin;
    
    /**
     * Factory for creating the session cache service of the application.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class SessionCacheServiceFactory
    {
        /**
         * Creates a session cache service from the container.
         *
         * @param string $module The module for which to create the service, defaults to the global service of the given library.
         * @param string $library The library for which to create the service, defaults to "alder".
         *
         * @return \Zend\Cache\Storage\StorageInterface The caching object for database entries.
         */
        public static function create(string $module = null, string $library = null) : StorageInterface {
            $container = DiContainer::get();
    
            if (!$module) {
                $cacheConfig = $container->get("config")[($library ?: "alder")]["session"]["cache"];
            } else {
                $cacheConfig = $container->get("config")[($library ?: "alder")][$module]["session"]["cache"];
            }
            
            $options = [
                "adapter"   => $cacheConfig["adapter"],
                "options"   => [
                    "ttl"    => $cacheConfig["time_to_live"],
                    "server" => $cacheConfig["server"]
                ],
                "namespace" => $cacheConfig["namespace"]
            ];
            if (isset($cacheConfig["password"])) {
                $options["options"]["password"] = $cacheConfig["password"];
            }
            
            $cache = StorageFactory::factory($options);
            
            $pluginsConfig = $cacheConfig["plugins"];
            $pluginOptions = new Plugin\PluginOptions();
            
            if (isset($pluginsConfig["ignore_user_abort"])) {
                $pluginOptions->setExitOnAbort($pluginsConfig["ignore_user_abort"]["exit_on_abort"]);
    
                $ignoreUserAbort = new Plugin\IgnoreUserAbort();
                $ignoreUserAbort->setOptions($pluginOptions);
                $cache->addPlugin($ignoreUserAbort);
            }
            
            return $cache;
        }
    }
