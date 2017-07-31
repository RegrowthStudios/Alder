<?php

    /**
     * The specific configuration of the Alder Public Authentication module.
     */

    return [
        "alder" => [
            //"email" => [ /* Email Settings */
            //    "transport" => "smtp", // Which method to send emails via. SMTP reduces chances of email being treated as spam vs. Sendmail.
            //    "options" => [ /* Transport Settings */
            //        "name" => "example.com", // The name of the SMTP server.
            //        "host" => "127.0.0.1", // The host IP of the SMTP server.
            //        "port" => 25, // Port to connect to. Usually 25 for insecure, 587 for TLS and 465 for SSL.
            //        "connection" => [ /* Connection Settings */
            //            "class" => "smtp", // The method of connecting to the SMTP server. Options: "smtp", "plain", "login", and "crammd5"
            //            "username" => "", // Username of user on SMTP server.
            //            "password" => "", // Password of user on SMTP server.
            //            "ssl" => "", // SSL method to use.
            //        ]
            //    ],
            //],
            "public_authentication" => [ /* Module-specific Settings */
                "username" => [ /* Username Settings */
                    "min_length" => 1, // Minimum length of usernames.
                    "max_length" => 32 // Maximum length of usernames.
                ],
                "password" => [ /* Password Settings */
                    "strictness" => PASSWORD_STRICTNESS_NORMAL, // How secure passwords must be. PASSWORD_STRICTNESS_NORMAL -> include numbers and letters. PASSWORD_STRICTNESS_HIGH -> PASSWORD_STRICTNESS_NORMAL + include capital letter. PASSWORD_STRICTNESS_STRICT -> PASSWORD_STRICTNESS_HIGH + include symbol. Retroactively checks passwords.
                    "hashing_strength" => 11, // How strong the hashing of the passwords should be.
                    "min_length" => 8, // Minimum length of passwords.
                    "max_length" => 48, // Maximum length of passwords.
                ],
                "user_session" => [ /* Session Settings */
                    "duration" => 43200/*12 Hours*/, // How long should a standard length log-in session last.
                    "duration_extended" => 2592000/*~1 Month*/, // How long should an extended log-in session last.
                    "cache" => [ /* Session Cache Settings */
                        "adapter" => [ /* Adapter Settings */
                            "name" => "redis",
                            "options" => [
                                "ttl" => 1800/*30 Mins*/, // How long does data last in cache for?
                                "namespace" => "alder_session", // Namespace of the cache items.
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
                    ]
                ],
                "client_auth_code" => [ /* Client Authorisation Code Settings */
                    "duration" => 7200/*2 Hours*/, // How long should an authorisation code last?
                    // TODO(Matthew): Double check but no auth code state needed on server, right?
                    //"cache" => [ /* Client Authorisation Code Cache Settings */
                    //    "namespace" => "alder_client_auth_cache", // Namespace in which all client auth code data is cached.
                    //    "time_to_live" => 7260/*2 Hours 1 Minute */,
                    //]
                ],
                "client_access_token" => [ /* Client Access Token Settings */
                    "duration" => 7200/*2 Hours*/, // How long should an authorisation code last?
                ],
                "user_access_token" => [ /* User Access Token Settings */
                    "duration" => 7200/*2 Hours*/, // How long should an authorisation code last?
                ]
            ],
            "language" => [ /* Language Settings */
                "language_sources" => [ /* Language Source File Settings */
                    "file_patterns" => [ /* File patterns for language files to load from. */
                        [
                            "type" =>  \Zend\I18n\Translator\Loader\PhpArray::class,
                            "base_dir" => LANGUAGE_DIRECTORY,
                            "pattern" => file_build_path("oauth", "%s.php"),
                            "text_domain" => "oauth"
                        ]
                    ]
                ]
            ]
        ]
    ];
