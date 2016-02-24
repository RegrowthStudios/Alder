<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
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
    
    use Zend\Mvc\ModuleRouteListener;
    use Zend\Mvc\MvcEvent;
    use Zend\View\Model\JsonModel;
    
    /**
     * Module class for Sycamore.
     */
    class Module
    {
        /**
         * Initialises listeners during the bootstrap process.
         * 
         * @param MvcEvent $e
         */
        public function onBootstrap(\Zend\Mvc\MvcEvent $e)
        {
            define("SYCAMORE_MODULE_DIRECTORY", MODULES_DIRECTORY."/Sycamore");
            
            $eventManager = $e->getApplication()->getEventManager();
            $moduleRouteListener = new ModuleRouteListener();
            $moduleRouteListener->attach($eventManager);
            
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array ($this, "onDispatchError"), 0);
            $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array ($this, "onRenderError"), 0);
        }
        
        /**
         * Returns a JSON model of the dispatch error in the provided event. Returns void if no error exists.
         * 
         * @param MvcEvent $e
         * 
         * @return mixed
         */
        public function onDispatchError(\Zend\Mvc\MvcEvent $e)
        {
            return $this->getJsonModelError($e);
        }
        
        /**
         * Returns a JSON model of the render error in the provided event. Returns void if no error exists.
         * 
         * @param MvcEvent $e
         * 
         * @return mixed
         */
        public function onRenderError(\Zend\Mvc\MvcEvent $e)
        {
            return $this->getJsonModelError($e);
        }
        
        // TODO(Matthew): Record stacktrace in log, do NOT send to client.
        /**
         * Returns a JSON model of the error in the provided event. Returns void if no error exists.
         * 
         * @param MvcEvent $e
         * 
         * @return mixed
         */
        public function getJsonModelError(\Zend\Mvc\MvcEvent $e)
        {
            $error = $e->getError();
            if (!$error) {
                return;
            }
            
            $exception = $e->getParam("exception");
            $exceptionJson = array();
            if ($exception) {
                $exceptionJson = array (
                    "class" => get_class($exception),
                    "file" => $exception->getFile(),
                    "line" => $exception->getLine(),
                    "message" => $exception->getMessage(),
                    "stacktrace" => $exception->getTraceAsString()
                );
            }
            
            $errorJson = array (
                "message" => "An error occurred during execution. Please try again later.",
                "error" => $error,
                "exception" => $exceptionJson
            );
            if ($error == 'error-router-no-match') {
                $errorJson['message'] = 'Resource not found.';
            }
            
            $model = new JsonModel(array('errors' => array($errorJson)));

            $e->setResult($model);

            return $model;
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
            return array (
                "Zend\Loader\StandardAutoloader" => array (
                    "namespaces" => array (
                        "Sycamore" => SYCAMORE_MODULE_DIRECTORY."/src/Sycamore",
                    )
                )
            );
        }
    }