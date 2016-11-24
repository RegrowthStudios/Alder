<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        "dependencies" => [
            "invokables" => [
                \Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
            ]
        ],

        // TODO(Matthew): Ensure all paths are of valid format.
        "routes" => [
            [
                "name" => "auth",
                "path" => "public_auth/auth",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\AuthenticateAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "user",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/public_auth/user(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\UserAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "user_license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/public_auth/user/license(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\UserLicenseAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/public_auth/license(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\LicenseAction([ "module" => "public_authentication" ])
                ]
            ],
        ],
    ];
