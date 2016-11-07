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
                \Zend\Expressive\Helper\ServerUrlHelper::class => Zend\Expressive\Helper\ServerUrlHelper::class,
            ],
            // Use "factories" for services provided by callbacks/factory classes.
            "factories" => [
                \Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
                \Zend\Expressive\Helper\UrlHelper::class => Zend\Expressive\Helper\UrlHelperFactory::class,
                \Zend\Db\Adapter\Adapter::class => function (\Interop\Container\ContainerInterface $container) {
                    $config = $container->get("config")["alder"];
                    return new \Zend\Db\Adapter\Adapter($config["db"]["adapter"]);
                },
                \Zend\Db\Metadata\MetadataInterface::class => function (\Interop\Container\ContainerInterface $container) {
                    $adapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    return \Zend\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);
                },
                "AlderDbCache" => \Alder\Cache\DatabaseCacheServiceFactory::class,
                "AlderTableCache" => \Alder\Cache\TableCacheServiceFactory::class,
                "AlderSessionCache" => \Alder\Cache\SessionCacheServiceFactory::class
            ],
        ],
    ];
