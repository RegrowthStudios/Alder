<?php

    // Define a bunch of directory constants.
    define("APP_DIRECTORY", dirname(__DIR__));
    define("SRC_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."src");
    define("VENDOR_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."vendor");
    define("CONFIG_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."config");
    define("LOGS_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."logs");
    define("TEMP_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."temp");
    define("CACHE_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."cache");
    define("ALDER_SRC_DIRECTORY", SRC_DIRECTORY.DIRECTORY_SEPARATOR."Alder");
    define("PUBLIC_AUTHENTICATION_SRC_DIRECTORY", ALDER_SRC_DIRECTORY.DIRECTORY_SEPARATOR."PublicAuthentication");
    // Define possible ENV states.
    define("PRODUCTION", "production");
    define("TESTING", "testing");
    define("DEVELOPMENT", "development");
    // Define ENV - the environment state (development or production).
    // ENV state stored in a PHP file to get around Nginx not supporting setting environment variables a la Apache.
    define("ENV", require CONFIG_DIRECTORY."/env.state.php");
    // Define value to set config values to that MUST be overridden by a given installation.
    define("DEFAULT_VAL", "CHANGE");
    
    // Define password strictness consts.
    define("PASSWORD_STRICTNESS_NORMAL", "normal");
    define("PASSWORD_STRICTNESS_HIGH", "high");
    define("PASSWORD_STRICTNESS_STRICT", "strict");