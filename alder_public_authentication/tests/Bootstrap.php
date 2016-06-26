<?php
    namespace AlderTest;

    use Zend\Loader\AutoloaderFactory;
    use Zend\Mvc\Service\ServiceManagerConfig;
    use Zend\ServiceManager\ServiceManager;

    /**
     * Test bootstrap, for setting up autoloading and service manager.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Bootstrap
    {
        /**
         * The service manager for testing with.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface 
         */
        protected static $serviceManager;

        /**
         * Prepares modules and the service manager.
         */
        public static function init()
        {
            $moduleDirs = glob(MODULES_DIRECTORY."/*", GLOB_ONLYDIR);
            foreach($moduleDirs as $key => $moduleDir) {
                $explode = explode("/", $moduleDir);
                $moduleDirs[$key] = end($explode);
            }

            static::initAutoloader();

            // Use ModuleManager to load the modules and their dependencies.
            $config = [
                "module_listener_options" => [
                    "module_paths" => [
                        VENDOR_DIRECTORY,
                        MODULES_DIRECTORY
                    ],
                    "config_glob_paths" => [
                        sprintf(CONFIG_DIRECTORY . "/autoload/{,*.}{global,%s,local}.php", ENV),
                    ],
                    "config_cache_enabled" => false,
                    "config_cache_key" => "sycamore_config",
                    "module_map_cache_enabled" => false,
                    "module_map_cache_key" => "sycamore_module_map",
                    "cache_dir" => CACHE_DIRECTORY . "/config",
                    "check_dependencies" => true,
                ],
                "modules" => $moduleDirs
            ];

            $serviceManager = new ServiceManager((new ServiceManagerConfig())->toArray());
            $serviceManager->setService("ApplicationConfig", $config);
            $serviceManager->get("ModuleManager")->loadModules();
            static::$serviceManager = $serviceManager;
        }

        /**
         * Changes directory to root app directory.
         */
        public static function chroot()
        {
            chdir(APP_DIRECTORY);
        }

        /**
         * Returns the service manager for this testing instance.
         * 
         * @return \Zend\ServiceManager\ServiceLocatorInterface
         */
        public static function &getServiceManager()
        {
            return static::$serviceManager;
        }

        /**
         * Initialises the autoloader for modules and ZF.
         * 
         * @throws \RuntimeException If ZendFramework was not correctly loaded.
         */
        protected static function initAutoloader()
        {
            if (file_exists(VENDOR_DIRECTORY."/autoload.php")) {
                include VENDOR_DIRECTORY."/autoload.php";
            }

            if (!class_exists("Zend\Loader\AutoloaderFactory")) {
                throw new \RuntimeException("Unable to load ZF2. Run `php composer.phar install`");
            }

            AutoloaderFactory::factory(array(
                "Zend\Loader\StandardAutoloader" => array(
                    "autoregister_zf" => true,
                    "namespaces" => array(
                        __NAMESPACE__ => __DIR__,
                    ),
                ),
            ));
        }
    }