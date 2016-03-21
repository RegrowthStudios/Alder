<?php

/*
 * All rights reserved.
 * 
 * Copyright (c) 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 */

    namespace SycamoreTest;

    error_reporting(E_ALL | E_STRICT);
    chdir(__DIR__);

    // Define a bunch of directory constants.
    define("APP_DIRECTORY", dirname(__DIR__));
    define("MODULES_DIRECTORY", APP_DIRECTORY."/modules");
    define("VENDOR_DIRECTORY", APP_DIRECTORY."/vendor");
    define("CONFIG_DIRECTORY", APP_DIRECTORY."/config");
    define("LOGS_DIRECTORY", APP_DIRECTORY."/logs");
    define("TEMP_DIRECTORY", APP_DIRECTORY."/temp");
    define("CACHE_DIRECTORY", APP_DIRECTORY."/cache");
    define("SYCAMORE_MODULE_DIRECTORY", MODULES_DIRECTORY."/Sycamore");

    // Define possible ENV states.
    define("PRODUCTION", "production");
    define("TESTING", "testing");
    define("DEVELOPMENT", "development");

    // Define ENV - the environment state (development or production).
    // ENV state stored in a PHP file to get around Nginx not supporting setting environment variables a la Apache.
    define("ENV", require (CONFIG_DIRECTORY . "/env.state.php"));

    // Define value to set config values to that MUST be overridden by a given installation.
    define("DEFAULT_VAL", "CHANGE");

    // Define appropriate OS constant.
    define("UNIX", "Unix");
    define("WINDOWS", "Windows");
    switch (php_uname("s")) {
        // Treat FreeBSD and Linux as basically UNIX because they"ll be treated the same as UNIX OS"s anyway.
        case "FreeBSD":
        case "Linux":
        case "Solaris":
            define("OS", UNIX);
            break;
        case "Windows NT":
            define("OS", WINDOWS);
            break;
    }
    
    require "./Bootstrap.php";
    
    Bootstrap::init();
    Bootstrap::chroot();