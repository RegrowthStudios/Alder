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
    
    class Module
    {
        public function onBootstrap(\Zend\Mvc\MvcEvent $e)
        {
            $eventManager = $e->getApplication()->getEventManager();
            $moduleRouteListener = new ModuleRouteListener();
            $moduleRouteListener->attach($eventManager);
        }
        
        public function getConfig()
        {
            return include (__DIR__ . "/config/module.config.php");
        }
        
        public function getAutoloaderConfig()
        {
            return array (
                "Zend\Loader\StandardAutoloader" => array (
                    "namespaces" => array (
                        "Sycamore" => __DIR__ . "/src/Sycamore",
                    )
                )
            );
        }
    }