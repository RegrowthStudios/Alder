<?php
    
    namespace Alder\Cache;
    
    use Interop\Container\ContainerInterface;
    
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
         * @param \Interop\Container\ContainerInterface $container The container for the application instance.
         *
         * @return \Zend\Cache\Storage\StorageInterface The caching object for database entries.
         */
        public function __invoke(ContainerInterface $container) : StorageInterface {
            $cacheConfig = $container->get("config")["alder"]["public_authentication"]["session"]["cache"];
            
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
            $pluginOptions = new Plugin\PluginOptions([
                                                          "ExitOnAbort" => $pluginsConfig["ignore_user_abort"]["exit_on_abort"],
                                                      ]);
            
            $ignoreUserAbort = new Plugin\IgnoreUserAbort();
            $ignoreUserAbort->setOptions($pluginOptions);
            $cache->addPlugin($ignoreUserAbort);
            
            return $cache;
        }
    }
