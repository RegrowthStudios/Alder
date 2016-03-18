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
    
    return array (
        "router" => array (
            "routes" => array (
                "api_user" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/user[/:controller]",
                        "defaults" => array (
                            "__NAMESPACE__" => "Sycamore\Controller\API\User",
                            "controller" => "Sycamore\Controller\API\User\Index",
                        ),
                    ),
                ),
                "api_newsletter" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/newsletter[/:controller]",
                        "defaults" => array (
                            "__NAMESPACE__" => "Sycamore\Controller\API\Newsletter",
                            "controller" => "Sycamore\Controller\API\Newsletter\Index",
                        ),
                    ),
                ),
                "api_attachment" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/attachment[/:controller]",
                        "defaults" => array (
                            "__NAMESPACE__" => "Sycamore\Controller\API\Attachment",
                            "controller" => "Sycamore\Controller\API\Attachment\Index",
                        ),
                    ),
                ),
            ),
        ),
        "service_manager" => array (
            "factories" => array (
                "DbAdapter" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    $config = $serviceManager->get("Config")["Sycamore"];
                    $adapter = new \Zend\Db\Adapter\Adapter($config["db"]["adapter"]);
                    
                    \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);
                    
                    return $adapter;
                },
                "Mailer" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\Mail\Mailer($serviceManager);
                },
                "Scheduler" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\Scheduler\Scheduler();                    
                }
            ),
        ),
        "controllers" => array (
            "invokables" => array (
                "Sycamore\Controller\API\User\Index" => "Sycamore\Controller\API\User\IndexController",
                "Sycamore\Controller\API\User\Ban" => "Sycamore\Controller\API\User\BanController",
                "Sycamore\Controller\API\Newsletter\Index" => "Sycamore\Controller\API\Newsletter\IndexController",
                "Sycamore\Controller\API\Newsletter\Subscriber" => "Sycamore\Controller\API\Newsletter\SubscriberController",
                "Sycamore\Controller\API\Attachment\Index" => "Sycamore\Controller\API\Attachment\IndexController",
            ),
        ),
    );
