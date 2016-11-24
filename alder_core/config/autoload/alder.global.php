<?php

    return [
        "alder" => [
            "app_name" => "Alder", // Title of the application.
            "default_locale" => "en_GB", // The default locale of the application. (http://www.roseindia.net/tutorials/I18N/locales-list.shtml has a list of possible locales.)
            "domain" => "example.com", // Domain of the application.
            // TODO(Matthew): Consider if all these security settings should be global.
            "security" => [ /* General Security Settings */
                "access_cookies_via_http_only" => true, // If true, cookies are only accessible via the HTTP protocol.
                "cookies_over_https_only" => true, // If true, cookies may only be sent to the user over a secure HTTPS connection.
                // TODO(Matthew): Implement.
                "enable_clickjacking_protection" => true, // If true, prevents clickjacking attacks by not allowing site to be rendered in iframe.
                "simple_hash_algorithm" => "sha256", // The hashing algorithm to be used for simple hashes - no sensitive data is hashed using this.
                "refresh_sessions_with_expiry_within" => 1296000/*0.5 Months*/, // Refresh sessions that interact with the server where their expiry will occur within the specified time.
            ]
        ]
    ];
