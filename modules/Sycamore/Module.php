<?php

/* 
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    namespace Sycamore;
    
    use Sycamore\Cache\TableCache;
    use Sycamore\Serialiser\API;
    
    use Zend\Cache\Storage\Plugin;
    use Zend\Cache\StorageFactory;
    use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
    use Zend\ModuleManager\Feature\ConfigProviderInterface;
    use Zend\Mvc\Application;
    use Zend\Mvc\ModuleRouteListener;
    use Zend\Mvc\MvcEvent;
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Module class for Sycamore.
     */
    class Module implements AutoloaderProviderInterface, ConfigProviderInterface
    {
        /**
         * Define the module directory constant.
         */
        public function __construct()
        {
            define("SYCAMORE_MODULE_DIRECTORY", MODULES_DIRECTORY."/Sycamore");
        }
        
        /**
         * Initialises listeners during the bootstrap process.
         * 
         * @param MvcEvent $e
         */
        public function onBootstrap(\Zend\Mvc\MvcEvent $e)
        {
            $eventManager = $e->getApplication()->getEventManager();
            $moduleRouteListener = new ModuleRouteListener();
            $moduleRouteListener->attach($eventManager);
            
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,  [$this, "onDispatchError"], 10);
            
            $serviceManager = $e->getApplication()->getServiceManager();
            $this->prepareServices($serviceManager);
        }
        
        // TODO(Matthew): Use a language object for strings presented to user.
        /**
         * Returns a JSON model of the dispatch error in the provided event. Returns void if no error exists.
         * 
         * @param MvcEvent $e
         * 
         * @return mixed
         */
        public function onDispatchError(\Zend\Mvc\MvcEvent $e)
        {
            $e->stopPropagation();
            $response = $e->getResponse();
            $exception = $e->getParam("exception");
            if ($e->getError() == Application::ERROR_ROUTER_NO_MATCH) {
                $response->setStatusCode(404);
                $response->setContent(API::encode(["error" => "No route match was found for the given URI."]));
            } else if ($e->getError() == Application::ERROR_CONTROLLER_NOT_FOUND 
                    || $e->getError() == Application::ERROR_CONTROLLER_INVALID
                    || $e->getError() == Application::ERROR_CONTROLLER_CANNOT_DISPATCH
                    || $e->getError() == Application::ERROR_EXCEPTION
                    || $exception) {
                if ($exception) {
                    $e->getApplication()->getServiceManager()->get("Logger")->crit($exception);
                }
                $response->setStatusCode(500);
                if (ENV != PRODUCTION) {
                    $response->setContent(API::encode(["error" => "A critical error: \n    " . $e->getError() . "\nhas occurred in processing this request. Please contact the service provider."]));
                } else {
                    $response->setContent(API::encode(["error" => "A critical error has occurred in processing this request. Please contact the service provider."]));
                }
            }
            return $response;
        }
                
        /**
         * Returns an array of configuration options for the module.
         * 
         * @return array
         */
        public function getConfig()
        {
            return include SYCAMORE_MODULE_DIRECTORY."/config/module.config.php";
        }
        
        /**
         * Returns an array of configuration options for the autoloader for this module.
         * 
         * @return array
         */
        public function getAutoloaderConfig()
        {
            return [
                "Zend\Loader\StandardAutoloader" => [
                    "namespaces" => [
                        "Sycamore" => SYCAMORE_MODULE_DIRECTORY."/src/Sycamore",
                    ]
                ]
            ];
        }
        
        /**
         * Prepares all of the services required by the Sycamore module.
         * 
         * @param ServiceManager $serviceManager The service manager to attach the created services to.
         */
        protected function prepareServices(ServiceManager& $serviceManager)
        {
            $this->createDatabaseCacheService($serviceManager);
            $this->createSycamoreTableCacheService($serviceManager);
        }
        
        /**
         * Creates the database caching service.
         * 
         * @param ServiceManager $serviceManager The service manager to attach the database caching services to.
         */
        protected function createDatabaseCacheService(ServiceManager& $serviceManager)
        {
            $cacheConfig = $serviceManager->get("Config")["Sycamore"]["cache"];
            
            $cache = StorageFactory::factory( [
                "adapter" => $cacheConfig["adapter"],
                "options" => [
                    "ttl" => $cacheConfig["timeToLive"],
                ],
                "namespace" => $cacheConfig["namespace"]
            ]);
            
            $pluginsConfig = $cacheConfig["plugins"];
            
            $clearExpired = new Plugin\ClearExpiredByFactor();
            $clearExpired->setOptions([
                "clearing_factor" => $pluginsConfig["clearExpired"]["clearingFactor"]
            ]);
            $cache->addPlugin($clearExpired);
            
            $ignoreUserAbort = new Plugin\IgnoreUserAbort();
            $ignoreUserAbort->setOptions([
                "exit_on_abort" => $pluginsConfig["ignoreUserAbort"]["exitOnAbort"]
            ]);
            $cache->addPlugin($ignoreUserAbort);
            
            $optimise = new Plugin\OptimizeByFactor();
            $optimise->setOptions([
                "optimizing_factor" => $pluginsConfig["optimise"]["optimisingFactor"]
            ]);
            $cache->addPlugin($optimise);
            
            $serialiser = new Plugin\Serializer();
            $cache->addPlugin($serialiser);
            
            $serviceManager->setService("DbCache", $cache);
        }
        
        /**
         * Creates the Sycamore table caching service.
         * 
         * @param ServiceManager $serviceManager The service manager to attach the Sycamore table caching services to.
         */
        protected function createSycamoreTableCacheService(ServiceManager& $serviceManager)
        {
            $tableCache = new TableCache($serviceManager, "Sycamore\\Table\\");
            
            $serviceManager->setService("SycamoreTableCache", $tableCache);
        }
    }