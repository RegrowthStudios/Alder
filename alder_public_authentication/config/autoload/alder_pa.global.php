<?php

    /**
     * The specific configuration of the Alder Public Authentication application.
     */

    return [
        "alder" => [
            "app_name" => "Alder Public Authenitcation", // Title of the application.
            "db" => [ /* Database Settings */
                "adapter" => [ /* Adapter Settings */
                    "driver" => "Pdo_Mysql", // The driver of the database. Values: "Mysqli", "Sqlsrv", "Pdo_Sqlite", "Pdo_Mysql",...
                    "database" => "", // The name of the database for the Sycamore application.
                    "hostname" => "localhost", // The host IP of the database.
                    "port" => "3306", // The port over which to connect to the database.
                    "username" => "", // The username with which to connect to the database.
                    "password" => "", // The password with which to connect to the database.
                    "charset" => "utf8", // The charset to use in communicating with database.
                ],
                "cache" => [ /* Cache Settings */
                    "namespace" => "alder_db_cache", // Namespace in which all application data is cached.
                    "time_to_live" => 1800/*30 Mins*/, // How long does the data live for in cache?
                    "adapter" => "filesystem", // Name of the type of cache to use.
                    "plugins" => [ /* Cache Plugin Details */
                        "clear_expired" => [ /* Cache Clearing Plugin */
                            "clearing_factor" => 100, // The probability that the clearing function will be called on a caching operation (1/n, where n is the value here).
                        ],
                        "ignore_user_abort" =>  [ /* User Abort Plugin */
                            "exit_on_abort" => false, // Whether the cache script should be aborted on user closing connection with server.
                        ],
                        "optimise" => [ /* Optimisation Plugin */
                            "optimising_factor" => 100, // The probability that the optimisation function will be called on a caching operation (1/n, where n is the value here).
                        ]
                    ],
                ],
                "force_db_fetch" => false, // Whether to force DB fetches and skip the cache. This is NOT recommended outside of development.
                "table_prefix" => "", // The prefix to be added to all table names for the application.
            ],
            "default_locale" => "en_GB", // The default locale of the application. (http://www.roseindia.net/tutorials/I18N/locales-list.shtml has a list of possible locales.)
            "domain" => "example.com", // Domain of the application.
            "email" => [ /* Email Settings */
                "transport" => "smtp", // Which method to send emails via. SMTP reduces chances of email being treated as spam vs. Sendmail.
                "options" => [ /* Transport Settings */
                    "name" => "example.com", // The name of the SMTP server.
                    "host" => "127.0.0.1", // The host IP of the SMTP server.
                    "port" => 25, // Port to connect to. Usually 25 for insecure, 587 for TLS and 465 for SSL.
                    "connection" => [ /* Connection Settings */
                        "class" => "smtp", // The method of connecting to the SMTP server. Options: "smtp", "plain", "login", and "crammd5"
                        "username" => "", // Username of user on SMTP server.
                        "password" => "", // Password of user on SMTP server.
                        "ssl" => "", // SSL method to use.
                    ]
                ],
            ],
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
                "session" => [ /* Session Settings */
                    "duration" => 43200/*12 Hours*/, // How long should a standard length log-in session last.
                    "duration_extended" => 2592000/*~1 Month*/, // How long should an extended log-in session last.
                    "cache" => [ /* Session Cache Settings */
                        "namespace" => "alder_session_cache", // Namespace in which all session data is cached.
                        "time_to_live" => 1800/*30 Mins*/, // How long does the data live for in cache?
                        "adapter" => "redis", // Name of the type of cache to use.
                        "server" => "/path/to/sock.sock", // Server address. Either socket path, or associative array of host, port and timeout.
                        "password" => null, // The password of the redis server, if there is one. NULL indicates no password.
                        "plugins" => [ /* Cache Plugin Details */
                            "ignore_user_abort" =>  [ /* User Abort Plugin */
                                "exit_on_abort" => false, // Whether the cache script should be aborted on user closing connection with server.
                            ]
                        ],
                    ]
                ],
                "token" => [ /* Token Settings */
                    "private_key" => DEFAULT_VAL, // The key used for signing JWTs. Do NOT share. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the private RSA key file.
                    "private_key_passphrase" => DEFAULT_VAL, // The passphrase for the private key, if it exists.
                    "public_key" => DEFAULT_VAL, // The key used for verifying JWTs IF an asymmetric hash algorithm is specified. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the public RSA key file.
                    "hash_algorithm" => "HS256", // The hashing algorithm used for the signing of JWTs. Allowed values: HS256, HS384, HS512, RS256, RS384, RS512. HS values are Hmac symmetric key methods, RS values are asymmetric RSA public/private key methods.
                ]
            ],
            "security" => [ /* General Security Settings */
                "access_cookies_via_http_only" => false, // If true, cookies are only accessible via the HTTP protocol.
                "cookies_over_https_only" => true, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                //"enable_clickjacking_protection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in iframe.
                "simple_hash_algorithm" => "sha256", // The hashing algorithm to be used for simple hashes - no sensitive data is hashed using this.
                "refresh_sessions_with_expiry_within" => 1296000/*0.5 Months*/, // Refresh sessions that interact with the server where their expiry will occur within the specified time.
            ]
        ]
    ];
