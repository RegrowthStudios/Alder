<?php
    
    /**
     * The middleware pipeline configuration. This works in concert with the routes
     * configuration to determine the specific application pipeline to take for a given request.
     */
    
    return [
        "dependencies" => [
            "invokables" => [
                \Alder\Middleware\LocalisationMiddleware::class => \Alder\Middleware\LocalisationMiddleware::class,
            ],
            "factories" => [
                \Zend\Expressive\Helper\ServerUrlMiddleware::class => \Zend\Expressive\Helper\ServerUrlMiddlewareFactory::class,
                \Zend\Expressive\Helper\UrlHelperMiddleware::class => \Zend\Expressive\Helper\UrlHelperMiddlewareFactory::class,
            ],
        ],
        
        "middleware_pipeline" => [
            // An array of middleware to register. Each item is of the following
            // specification:
            //
            // [
            //  Required:
            //     "middleware" => "Name or array of names of middleware services and/or callables",
            //  Optional:
            //     "path"     => "/path/to/match", // string; literal path prefix to match
            //                                     // middleware will not execute
            //                                     // if path does not match!
            //     "error"    => true, // boolean; true for error middleware
            //     "priority" => 1, // int; higher values == register early;
            //                      // lower/negative == register last;
            //                      // default is 1, if none is provided.
            // ],
            //
            // While the ApplicationFactory ignores the keys associated with
            // specifications, they can be used to allow merging related values
            // defined in multiple configuration files/locations. This file defines
            // some conventional keys for middleware to execute early, routing
            // middleware, and error middleware.

            // Middleware for independent helpers..
            [
                "middleware" => [
                    \Zend\Expressive\Helper\ServerUrlMiddleware::class
                ],
                "priority" => 100000
            ],

            // Middleware for installation reroute and admin authentication.
            [
                "path" => "/admin",
                "middleware" => [
                    \Alder\Middleware\Admin\InstallNeededMiddleware::class,
                    \Alder\Middleware\Admin\AuthenticationMiddleware::class
                ],
                "priority" => 20000
            ],

            // Middleware for bootstrapping, pre-route conditions, global authentication and establishing pre-route helpers.
            [
                "middleware" => [
                    \Alder\Middleware\ApiMapMiddleware::class,
                    \Alder\Middleware\LocalisationMiddleware::class,
                    \Alder\Middleware\SessionMiddleware::class,
                ],
                "priority" => 10000
            ],

            // Middleware for fetching admin notifications.
            [
                "path" => "/admin",
                "middleware" => [
                    \Alder\Middleware\Admin\FetchAlertsMiddleware::class,
                    \Alder\Middleware\Admin\FetchTasksMiddleware::class,
                    \Alder\Middleware\Admin\FetchMessagesMiddleware::class
                ],
                "priority" => 5000
            ],
            
            // Middleware for route-based validation and authorisation.
            [
                "middleware" => [
                    \Zend\Expressive\Container\ApplicationFactory::ROUTING_MIDDLEWARE,
                    \Zend\Expressive\Helper\UrlHelperMiddleware::class,
                    \Zend\Expressive\Container\ApplicationFactory::DISPATCH_MIDDLEWARE,
                ],
                "priority" => 1,
            ],
            
            [
                "middleware" => [
                    // TODO(Matthew): Add error middleware here. 404 error?
                ],
                "error"    => true,
                "priority" => -10000,
            ],
        ],
    ];
