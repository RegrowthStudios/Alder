<?php

    return [
        "alder" => [
            "app_name" => "Alder", // Title of the application.
            "db" => [ /* Database Settings */
                "cache" => [ /* Database Cache Settings */
                    "adapter" => [ /* Adapter Settings */
                        "name" => "filesystem",
                        "options" => [
                            "ttl" => 1800/*30 Mins*/, // How long does data last in cache for?
                            "namespace" => "alder_db", // Namespace of the cache items.
                            "cache_dir" => CACHE_DIRECTORY, // Where should cache files be stored?
                        ]
                    ],
                    "plugins" => [ /* Plugin Settings */
                        [
                            "name" => "ignore_user_abort", // The name of the plugin.
                            "options" => [ /* Ignore User Abort Options */
                                "exit_on_abort" => false // Whether the cache script should exit on user aborting request.
                            ]
                        ],
                        [
                            "name" => "clear_expired_by_factor", // The name of the plugin.
                            "options" => [ /* Clear Expired Options */
                                "clearing_factor" => 100 // Once in this many times the cache will be cleared.
                            ]
                        ],
                        [
                            "name" => "optimize_by_factor", // The name of the plugin.
                            "options" => [ /* Optimise Options */
                                "optimizing_factor" => 100 // Once in this many times the cache will be optimised.
                            ]
                        ]
                    ]
                ]
            ],
            "language" => [ /* Language Settings */
                "cache" => [ /* Cache options for language files. */
                    "adapter" => [ /* Adapter Settings */
                        "name" => "filesystem",
                        "options" => [
                            "ttl" => 1800/*30 Mins*/, // How long does data last in cache for?
                            "namespace" => "alder_language", // Namespace of the cache items.
                            "cache_dir" => CACHE_DIRECTORY, // Where should cache files be stored?
                        ]
                    ],
                    "plugins" => [ /* Plugin Settings */
                        [
                            "name" => "ignore_user_abort", // The name of the plugin.
                            "options" => [ /* Ignore User Abort Options */
                                "exit_on_abort" => false // Whether the cache script should exit on user aborting request.
                            ]
                        ],
                        [
                            "name" => "clear_expired_by_factor", // The name of the plugin.
                            "options" => [ /* Clear Expired Options */
                                "clearing_factor" => 100 // Once in this many times the cache will be cleared.
                            ]
                        ],
                        [
                            "name" => "optimize_by_factor", // The name of the plugin.
                            "options" => [ /* Optimise Options */
                                "optimizing_factor" => 100 // Once in this many times the cache will be optimised.
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];
