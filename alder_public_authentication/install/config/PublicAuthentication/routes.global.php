<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        // TODO(Matthew): Ensure all paths are of valid format.
        "routes" => [
            [
                "name" => "login",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/login",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\LoginAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "auth",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/auth(/{response_type:(token|code)}(/{client_id:[a-zA-Z0-9]+}))",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\ClientAuthenticationAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "token",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/token(/{grant_type:(authorization_code|password|client_credentials)}(/{client_id:[a-zA-Z0-9]+}))",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\ClientAccessTokenAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "user",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/user(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\UserAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "user_license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/user/license(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\UserLicenseAction([ "module" => "public_authentication" ])
                ]
            ],
            [
                "name" => "license",
                "path" => "(/{locale:[a-z]{2}_[A-Z]{2}})/license(/{id:[0-9]+})",
                "middleware" => [
                    \Alder\PublicAuthentication\Middleware\AclMiddleware::class,
                    new \Alder\PublicAuthentication\Action\LicenseAction([ "module" => "public_authentication" ])
                ]
            ],
        ],
    ];
