<?php
    
    // Define a bunch of directory constants.
    define("APP_DIRECTORY", dirname(__DIR__));
    define("SRC_DIRECTORY", file_build_path(APP_DIRECTORY, "src"));
    define("VENDOR_DIRECTORY", file_build_path(APP_DIRECTORY, "vendor"));
    define("PUBLIC_DIRECTORY", file_build_path(APP_DIRECTORY, "public"));
    define("PUBLIC_ADMIN_DIRECTORY", file_build_path(PUBLIC_DIRECTORY, "admin"));
    define("INSTALL_DIRECTORY", file_build_path(APP_DIRECTORY, "install"));
    define("INSTALL_CONFIG_DIRECTORY", file_build_path(INSTALL_DIRECTORY, "config"));
    define("INSTALL_DATA_DIRECTORY", file_build_path(INSTALL_DIRECTORY, "data"));
    define("DATA_DIRECTORY", file_build_path(APP_DIRECTORY, "data"));
    define("CONFIG_DIRECTORY", file_build_path(APP_DIRECTORY, "config"));
    define("COMMON_CONFIG_DIRECTORY", file_build_path(CONFIG_DIRECTORY, "common"));
    define("PUBLIC_CONFIG_DIRECTORY", file_build_path(CONFIG_DIRECTORY, "public"));
    define("ADMIN_CONFIG_DIRECTORY", file_build_path(CONFIG_DIRECTORY, "admin"));
    define("LANGUAGE_DIRECTORY", file_build_path(APP_DIRECTORY, "i18n"));
    define("API_MAP_DIRECTORY", file_build_path(APP_DIRECTORY, "apimap"));
    define("LOGS_DIRECTORY", file_build_path(APP_DIRECTORY, "logs"));
    define("TEMP_DIRECTORY", file_build_path(APP_DIRECTORY, "temp"));
    define("CACHE_DIRECTORY", file_build_path(APP_DIRECTORY, "cache"));
    define("ALDER_SRC_DIRECTORY", file_build_path(SRC_DIRECTORY, "Alder"));
    
    // Define possible ENV states.
    define("PRODUCTION", "production");
    define("TESTING", "testing");
    define("DEVELOPMENT", "development");
    // Define ENV - the environment state (development or production).
    // ENV state stored in a PHP file to get around Nginx not supporting setting environment variables a la Apache.
    define("ENV", require file_build_path(CONFIG_DIRECTORY, "env.state.php"));
    
    // Define value to set config values to that MUST be overridden by a given installation.
    define("DEFAULT_VAL", "CHANGE");
    
    /* User Constants */
    define("USER_SESSION", "alis");
    // Define password strictness consts.
    define("PASSWORD_STRICTNESS_NORMAL", "normal");
    define("PASSWORD_STRICTNESS_HIGH", "high");
    define("PASSWORD_STRICTNESS_STRICT", "strict");
    
    // TODO(Matthew): Roles should not be hardcoded. Initialise ACL during installation with default roles...
    /* ACL Constants. */
    // Role strings
    define("GUEST", "guest");
    define("REGISTERED", "registered");
    define("MODERATOR", "moderator");
    define("ADMIN", "admin");
    define("SUPER_ADMIN", "super_admin");
    // Resource strings
    define("AUTHENTICATE", "authenticate");
    define("LICENSE", "license");
    define("LICENSE_TEXT", "license_text");
    define("LICENSE_LICENSE_TEXT_MAP", "license_license_text_map");
    define("USER", "user");
    define("USER_LICENSE_MAP", "user_license_map");
    // Privilege strings
    define("GET", "get");
    define("POST", "create");
    define("PUT", "replace");
    define("PATCH", "update");
    define("DELETE", "delete");
    define("OPTIONS", "options");
