<?php

    namespace Alder;
    
    return [
        "router" => [
            "routes" => [
                "user" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => [
                        "route" => "[/:locale]/user[/:id]",
                        "constraints" => [
                            "locale" => "[a-z]{2}_[A-Z]{2}",
                            "id" => "[0-9]+"
                        ],
                        "defaults" => [
                            "__NAMESPACE__" => "Alder\Controller\User",
                            "controller" => "Alder\Controller\User\Index",
                            "locale" => NULL,
                            "id" => NULL
                        ]
                    ]
                ],
                "user_license" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => [
                        "route" => "[/:locale]/user/license[/:id]",
                        "constraints" => [
                            "locale" => "[a-z]{2}_[A-Z]{2}",
                            "id" => "[0-9]+"
                        ],
                        "defaults" => [
                            "__NAMESPACE__" => "Alder\Controller\User",
                            "controller" => "Alder\Controller\User\License",
                            "locale" => NULL,
                            "id" => NULL
                        ]
                    ]
                ],
                "license" => [
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => [
                        "route" => "[/:locale]/license[/:id]",
                        "constraints" => [
                            "locale" => "[a-z]{2}_[A-Z]{2}",
                            "id" => "[0-9]+"
                        ],
                        "defaults" => [
                            "__NAMESPACE__" => "Alder\Controller\License",
                            "controller" => "Alder\Controller\Index",
                            "locale" => NULL,
                            "id" => NULL
                        ]
                    ]
                ]
            ]
        ]
    ];