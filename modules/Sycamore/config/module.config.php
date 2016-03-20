<?php

/*
 * All rights reserved.
 * 
 * Copyright (c) 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
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
                    
                    return $adapter;
                },
                "Sycamore\Mail\Mailer" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\Mail\Mailer($serviceManager);
                },
                "Sycamore\Scheduler\Scheduler\Scheduler" => function (\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\Scheduler\Scheduler();                    
                },
                "Sycamore\User\Security" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Security($serviceManager);
                },
                "Sycamore\User\Session" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Session($serviceManager);
                },
                "Sycamore\User\Validation" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Validation($serviceManager);
                },
                "Sycamore\User\Verify" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\User\Verify($serviceManager);
                },
                "Sycamore\Visitor" => function(\Zend\ServiceManager\ServiceLocatorInterface $serviceManager) {
                    return new \Sycamore\Visitor($serviceManager);
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
