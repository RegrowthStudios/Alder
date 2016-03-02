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
    
    use Sycamore\Serialiser\API;
    
    use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
    use Zend\ModuleManager\Feature\ConfigProviderInterface;
    use Zend\Mvc\Application;
    use Zend\Mvc\ModuleRouteListener;
    use Zend\Mvc\MvcEvent;
    use Zend\View\Model\JsonModel;
    
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
            
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array ($this, "onDispatchError"), 10);
            //$eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array ($this, "onRenderError"), 10);
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
            if ($e->getError() == Application::ERROR_ROUTER_NO_MATCH) {
                $response->setStatusCode(404);
                $response->setContent(API::encode(["error" => "No route match was found for the given URI."]));
            } else if ($e->getError() == Application::ERROR_CONTROLLER_NOT_FOUND 
                    || $e->getError() == Application::ERROR_CONTROLLER_INVALID
                    || $e->getError() == Application::ERROR_CONTROLLER_CANNOT_DISPATCH
                    || $e->getError() == Application::ERROR_EXCEPTION) {
                $response->setStatusCode(500);
                $response->setContent(API::encode(["error" => "A critical error has occurred in processing this request. Please contact the service provider."]));
            }
            return $response;
        }
        
//        /**
//         * Returns a JSON model of the render error in the provided event. Returns void if no error exists.
//         * 
//         * @param MvcEvent $e
//         * 
//         * @return mixed
//         */
//        public function onRenderError(\Zend\Mvc\MvcEvent $e)
//        {
//            return $this->getJsonModelError($e);
//        }
        
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
            return array (
                "Zend\Loader\StandardAutoloader" => array (
                    "namespaces" => array (
                        "Sycamore" => SYCAMORE_MODULE_DIRECTORY."/src/Sycamore",
                    )
                )
            );
        }
    }