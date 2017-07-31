<?php

    return [
        "alder" => [
            "app_name" => "Alder", // Title of the application.
            "db" => [ /* Database Settings */
                "adapter" => [ /* Db Adapter Settings */
                   "driver" => "Pdo_Mysql", // The driver of the database. Values: "Mysqli", "Sqlsrv", "Pdo_Sqlite", "Pdo_Mysql",...
                   "database" => "", // The name of the database for the Sycamore application.
                   "hostname" => "localhost", // The host IP of the database.
                   "port" => "3306", // The port over which to connect to the database.
                   "username" => "", // The username with which to connect to the database.
                   "password" => "", // The password with which to connect to the database.
                   "charset" => "utf8", // The charset to use in communicating with database.
                ],
                "cache" => [ /* Database Cache Settings */
                    "adapter" => [ /* Adapter Settings */
                        "name" => "redis",
                        "options" => [
                            "ttl" => 1800/*30 Mins*/, // How long does data last in cache for?
                            "namespace" => "alder_db", // Namespace of the cache items.
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
                "force_db_fetch" => false, // Whether to force DB fetches and skip the cache. This is NOT recommended outside of development.
                "table_prefix" => "alder_", // The prefix to be added to all table names for the application.
            ],
            "domain" => "example.com", // Domain of the application.
            // TODO(Matthew): Consider if all these security settings should be global.
            "security" => [ /* General Security Settings */
                "access_cookies_via_http_only" => true, // If true, cookies are only accessible via the HTTP protocol.
                "cookies_over_https_only" => true, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                // TODO(Matthew): Implement.
                "enable_clickjacking_protection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in iframe.
                "refresh_sessions_with_expiry_within" => 1296000/*0.5 Months*/, // Refresh sessions that interact with the server where their expiry will occur within the specified time.
            ],
            "token" => [ /* Default Token Settings */
                 "private_key" => DEFAULT_VAL, // The key used for signing JWTs. Do NOT share. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the private key file.
                 "private_key_passphrase" => DEFAULT_VAL, // The passphrase for the private key, if it exists.
                 "public_key" => DEFAULT_VAL, // The key used for verifying JWTs IF an asymmetric hash algorithm is specified. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the public key file. MUST be the same as private_key value if a symmetric signing method is chosen.
                 "sign_method" => \Lcobucci\JWT\Signer\Hmac\Sha384::class, // The algorithm used for the signing of JWTs. Allowed values: HS256, HS384, HS512, RS256, RS384, RS512. HS values are Hmac symmetric key methods, RS values are asymmetric RSA public/private key methods.
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
                "language_sources" => [ /* Language Source File Settings */
                    "file_patterns" => [ /* File patterns for language files to load from. */
                        [
                            "type" =>  \Zend\I18n\Translator\Loader\PhpArray::class,
                            "base_dir" => LANGUAGE_DIRECTORY,
                            "pattern" => file_build_path("core", "%s.php"),
                            "text_domain" => "core"
                        ]
                    ],
                    "files" => [ /* Specific language files to load. */ ],
                    "remote_files" => [ /* Specific remote language files to load. */ ]
                ]
            ]
        ],
    ];
