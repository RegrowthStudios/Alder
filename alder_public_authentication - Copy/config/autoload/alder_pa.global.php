<?php

    /**
     * Global configuration file for the Sycamore application.
     */
    return [
        "Sycamore" => [
            "cache" => [ /* Cache Details */
                "namespace" => "sycamore_cache", // Namespace in which all application data is cached.
                "timeToLive" => 1800/*30 Mins*/, // How long does the data live for in cache?
                "adapter" => "filesystem", // Name of the type of cache to use.
                "plugins" => [ /* Cache Plugin Details */
                    "clearExpired" => [ /* Cache Clearing Plugin */
                        "clearingFactor" => 100, // The probability that the clearing function will be called on a caching operation (1/n, where n is the value here).
                    ],
                    "ignoreUserAbort" =>  [ /* User Abort Plugin */
                        "exitOnAbort" => false, // Whether the cache script should be aborted on user closing connection with server.
                    ],
                    "optimise" => [ /* Optimisation Plugin */
                        "optimisingFactor" => 100, // The probability that the optimisation function will be called on a caching operation (1/n, where n is the value here).
                    ]
                ],
            ],
            "db" => [ /* Database Details */
                "adapter" => [
                    "driver" => "Pdo_Mysql", // The driver of the database. Values: "Mysqli", "Sqlsrv", "Pdo_Sqlite", "Pdo_Mysql",...
                    "database" => "", // The name of the database for the Sycamore application.
                    "host" => "localhost", // The host IP of the database.
                    "port" => "3306", // The port over which to connect to the database.
                    "username" => "", // The username with which to connect to the database.
                    "password" => "", // The password with which to connect to the database.
                    "charset" => "utf8", // The charset to use in communicating with database.
                ],
                "tablePrefix" => "", // The prefix to be added to all table names for the application.
                "forceDbFetch" => false, // Whether to force DB fetches and skip the cache. This is NOT recommended outside of development.
            ],
            "domain" => "example.com", // Domain of the application.
            "default_locale" => "en_GB", // The default locale used by the application. (http://www.roseindia.net/tutorials/I18N/locales-list.shtml has a list of possible locales.)
            "security" => [ /* Security Details */
                "accessCookiesViaHttpOnly" => true, // If true, cookies are only accessible via the HTTP protocol.
                "enableClickjackingProtection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in frame of another site.
                "password" => [ /* Password Security Details */
                    "strictness" => PASSWORD_STRICTNESS_NORMAL, // How secure must passwords be? PASSWORD_STRICTNESS_NORMAL -> include numbers and letters. PASSWORD_STRICTNESS_HIGH -> PASSWORD_STRICTNESS_NORMAL + include capital letter. PASSWORD_STRICTNESS_STRICT -> PASSWORD_STRICTNESS_HIGH + include symbol. Retroactively checks passwords.
                    "hashingStrength" => 11, // How strong should the hash be? Higher is stronger, but requires more CPU time.
                    "minimumLength" => 8, // The minimum length a password may be.
                    "maximumLength" => 48, // The maximum length a password may be. A value lesser than the minimum length implies no maximum length.
                ],
                "sessionsOverHttpsOnly" => false, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                "sessionLength" => 43200/*12 Hours*/, // How long should log in session last if not extended?
                "sessionLengthExtended" => 2629740,/*~1 Month*/ // How long should an extended log-in session last?
                "tokenPrivateKey" => DEFAULT_VAL, /* CHANGE THIS */ // The key used for signing JWTs. Do NOT share. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the private RSA key file.
                "tokenPublicKey" => DEFAULT_VAL, /* CHANGE THIS */ // The key used for verifying JWTs IF an asymmetric hash algorithm is specified. In case of asymmetric hash algorithm, this should be the file URI ("file://...") to the public RSA key file.
                "tokenHashAlgorithm" => "HS256", // The hashing algorithm used for the signing of JWTs. Allowed values: HS256, HS384, HS512, RS256, RS384, RS512. HS values are Hmac symmetric key methods, RS values are asymmetric RSA public/private key methods.
            ],
            "username" => [ /* Username Details */
                "minimumLength" => 1,
                "maximumLength" => 32,
            ],
        ],
    ];
