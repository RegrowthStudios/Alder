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
                    "host" => "localhost", // The host IP of the database.
                    "port" => "3306", // The port over which to connect to the database.
                    "username" => "", // The username with which to connect to the database.
                    "password" => "", // The password with which to connect to the database.
                    "charset" => "utf8", // The charset to use in communicating with database.
                ],
                "table_prefix" => "", // The prefix to be added to all table names for the application.
                "force_db_fetch" => false, // Whether to force DB fetches and skip the cache. This is NOT recommended outside of development.
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
                    "duration_extended" => 2629740/*~1 Month*/, // How long should an extended log-in session last.
                ],
                "token" => [ /* Token Settings */
                    "private_key" => DEFAULT_VAL, // The ky used for signing JWTs. Do NOT share. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the private RSA key file.
                    "public_key" => DEFAULT_VAL, // The key used for verifying JWTs IF an asymmetric hash algorithm is specified. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the public RSA key file.
                    "hash_algorithm" => "HS256", // The hashing algorithm used for the signing of JWTs. Allowed values: HS256, HS384, HS512, RS256, RS384, RS512. HS values are Hmac symmetric key methods, RS values are asymmetric RSA public/private key methods.
                ]
            ],
            "security" => [ /* General Security Settings */
                "access_cookies_via_http_only" => false, // If true, cookies are only accessible via the HTTP protocol.
                "cookies_over_https_only" => true, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                //"enableClickjackingProtection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in iframe.
                "simple_hash_algorithm" => "sha256", // The hashing algorithm to be used for simple hashes - no sensitive data is hashed using this.
            ]
        ]
    ];
