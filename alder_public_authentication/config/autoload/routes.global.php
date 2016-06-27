<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        "dependencies" => [
            "invokables" => [
                Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
    //            App\Action\PingAction::class => App\Action\PingAction::class,
            ],
            "factories" => [
    //            App\Action\HomePageAction::class => App\Action\HomePageFactory::class,
            ],
        ],

        "routes" => [
            [
                "name" => "user",
                "path" => "(/:locale)/user(/:id)",
                "middleware" => Alder\PublicAuthentication\Action\UserAction::class,
                "options" => [
                    "conditions" => [
                        "locale" => "[a-z]{2}_[A-Z]{2}",
                        "id" => "[0-9]+"
                    ]
                ]
            ],
            [
                "name" => "user_license",
                "path" => "(/:locale)/user/license(/:id)",
                "middleware" => Alder\PublicAuthentication\Action\UserLicenseAction::class,
                "options" => [
                    "conditions" => [
                        "locale" => "[a-z]{2}_[A-Z]{2}",
                        "id" => "[0-9]+"
                    ]
                ]
            ],
            [
                "name" => "license",
                "path" => "(/:locale)/license(/:id)",
                "middleware" => Alder\PublicAuthentication\Action\LicenseAction::class,
                "options" => [
                    "conditions" => [
                        "locale" => "[a-z]{2}_[A-Z]{2}",
                        "id" => "[0-9]+"
                    ]
                ]
            ],
        ],
    ];
