<?php

    /**
     * Global dependencies required by the public authentication module.
     */

    return [
        "dependencies" => [
            "factories" => [
                "alder_pa_table_cache" => function(\Interop\Container\ContainerInterface $container) {
                    return new \Alder\Db\TableCache("Alder\\PublicAuthentication\\Db\\Table\\");
                },
                "alder_pa_session_cache" => function(\Interop\Container\ContainerInterface $container) {
                    return \Alder\Cache\CacheServiceFactory::create("session", "public_authentication");
                }
            ],
        ],
    ];
