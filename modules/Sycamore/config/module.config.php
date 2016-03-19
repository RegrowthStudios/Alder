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
    
    return [
        "router" => [
            "routes" => [
                "api_user" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => [
                        "route" => "/api/user[/:controller]",
                        "defaults" => [
                            "__NAMESPACE__" => "Sycamore\Controller\API\User",
                            "controller" => "Sycamore\Controller\API\User\Index",
                        ],
                    ],
                ],
                "api_newsletter" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" =>  [
                        "route" => "/api/newsletter[/:controller]",
                        "defaults" => [
                            "__NAMESPACE__" => "Sycamore\Controller\API\Newsletter",
                            "controller" => "Sycamore\Controller\API\Newsletter\Index",
                        ],
                    ],
                ],
                "api_attachment" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => [
                        "route" => "/api/attachment[/:controller]",
                        "defaults" => [
                            "__NAMESPACE__" => "Sycamore\Controller\API\Attachment",
                            "controller" => "Sycamore\Controller\API\Attachment\Index",
                        ],
                    ],
                ],
            ],
        ],
        "service_manager" => [
            "factories" => [
                "Zend\Db\Adapter\Adapter" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    $config = $serviceManager->get("Config")["Sycamore"];
                    $adapter = new \Zend\Db\Adapter\Adapter($config["db"]["adapter"]);
                    
                    \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);
                    
                    return $adapter;
                },
                "Sycamore\User\Security" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Security($serviceManager);
                },
                "Sycamore\User\Session" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Session($serviceManager);
                }
            ],
        ],
        "controllers" => [
            "invokables" => [
                "Sycamore\Controller\API\User\Index" => "Sycamore\Controller\API\User\IndexController",
                "Sycamore\Controller\API\User\Ban" => "Sycamore\Controller\API\User\BanController",
                "Sycamore\Controller\API\Newsletter\Index" => "Sycamore\Controller\API\Newsletter\IndexController",
                "Sycamore\Controller\API\Newsletter\Subscriber" => "Sycamore\Controller\API\Newsletter\SubscriberController",
                "Sycamore\Controller\API\Attachment\Index" => "Sycamore\Controller\API\Attachment\IndexController",
            ],
        ],
    ];
