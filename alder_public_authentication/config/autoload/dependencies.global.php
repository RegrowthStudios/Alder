<?php

    /**
     * Global dependencies required by the entire application.
     * 
     * TODO(Matthew): Determine which of the current dependencies are actually needed.
     */

    return [
        "dependencies" => [
            // Use "invokables" for constructor-less services - i.e. no arguments need to be supplied to the constructor.
            "invokables" => [
                Zend\Expressive\Helper\ServerUrlHelper::class => Zend\Expressive\Helper\ServerUrlHelper::class,
            ],
            // Use "factories" for services provided by callbacks/factory classes.
            "factories" => [
                Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
                Zend\Expressive\Helper\UrlHelper::class => Zend\Expressive\Helper\UrlHelperFactory::class,
                "AlderDbCache" => Alder\PublicAuthentication\Db\DatabaseCacheServiceFactory::class,
                "AlderTableCache" => Alder\PublicAuthentication\Db\TableCacheServiceFactory::class
            ],
        ],
    ];
