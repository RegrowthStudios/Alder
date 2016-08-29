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

        // TODO(Matthew): Ensure all paths are of valid format.
        "routes" => [
            [
                "name" => "auth",
                "path" => "/auth",
                "middleware" => Alder\PublicAuthentication\Action\AuthenticateAction::class,
            ],
            [
                "name" => "user",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/user(/{id:[0-9]+})",
                "middleware" => Alder\PublicAuthentication\Action\UserAction::class
            ],
            [
                "name" => "user_license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/user/license(/{id:[0-9]+})",
                "middleware" => Alder\PublicAuthentication\Action\UserLicenseAction::class
            ],
            [
                "name" => "license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/license(/{id:[0-9]+})",
                "middleware" => Alder\PublicAuthentication\Action\LicenseAction::class
            ],
        ],
    ];
