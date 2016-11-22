<?php

    /**
     * Global dependencies required by the public authentication module.
     */

    return [
        "dependencies" => [
            "factories" => [
                "alder_pa_db_cache" => function(\Interop\Container\ContainerInterface $container) {
                    return \Alder\Cache\DatabaseCacheServiceFactory::create("public_authentication");
                },
                "alder_pa_table_cache" => function(\Interop\Container\ContainerInterface $container) {
                    return \Alder\Cache\TableCacheServiceFactory::create("Alder\\PublicAuthentication\\Db\\Table\\");
                },
                "alder_pa_session_cache" => function(\Interop\Container\ContainerInterface $container) {
                    return \Alder\Cache\DatabaseCacheServiceFactory::create("public_authentication");
                }
            ],
        ],
    ];
