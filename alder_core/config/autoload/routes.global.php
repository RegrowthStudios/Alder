<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        "dependencies" => [
            "invokables" => [
                \Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
            ]
        ]
    ];
