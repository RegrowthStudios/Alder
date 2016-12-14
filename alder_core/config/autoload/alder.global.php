<?php

    return [
        "alder" => [
            "app_name" => "Alder", // Title of the application.
            "domain" => "example.com", // Domain of the application.
            // TODO(Matthew): Consider if all these security settings should be global.
            "security" => [ /* General Security Settings */
                "access_cookies_via_http_only" => true, // If true, cookies are only accessible via the HTTP protocol.
                "cookies_over_https_only" => true, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                // TODO(Matthew): Implement.
                "enable_clickjacking_protection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in iframe.
                "refresh_sessions_with_expiry_within" => 1296000/*0.5 Months*/, // Refresh sessions that interact with the server where their expiry will occur within the specified time.
            ],
            "language" => [ /* Language Settings */
                "default_locale" => "en_GB", // The default locale of the application. (http://www.roseindia.net/tutorials/I18N/locales-list.shtml has a list of possible locales.)
                "fallback_locale" => "en_GB", // The locale to fallback to if the specified or default locale are missing a requested message string. NULL indicates no fallback locale.
                "cache" => [ /* Cache options for language files. */
                    "adapter" => [ /* Adapter Settings. */
                        "name" => "redis", // The adapter type.
                        "options" => [ /* Adapter Options. */
                            "ttl" => 43200/*12 Hours*/, // The time to live of items inserted into the cache.
                            "namespace" => "alder_language", // Namespace of the cache items.
                            "server" => "/path/to/sock.sock", // The server address of the cache. Can be either a URI, associative array or list as described at https://docs.zendframework.com/zend-cache/storage/adapter/#adapter-specific-options_4
                            "password" => "", // The password of the Redis server.
                            "database" => 0, // The database identifier of the cache on the Redis server.
                        ]
                    ],
                    "plugins" => [ /* Plugin Settings */
                        [
                            "name" => "ignore_user_abort", // The name of the plugin.
                            "options" => [ /* Ignore User Abort Options */
                                "exit_on_abort" => false // Whether the cache script should exit on user aborting request.
                            ],
                            "priority" => 1 // The priority this plugin takes over other plugins applied to the cache object.
                        ]
                    ]
                ],
                "language_sources" => [
                    "file_patterns" => [ /* File patterns for language files to load from. */
                        [
                            "type" =>  \Zend\I18n\Translator\Loader\PhpArray::class,
                            "base_dir" => LANGUAGE_DIRECTORY,
                            "pattern" => "%s.php"
                        ]
                    ],
                    "files" => [ /* Specific language files to load. */ ],
                    "remote_files" => [ /* Specific remote language files to load. */ ]
                ]
            ]
        ],
    ];
