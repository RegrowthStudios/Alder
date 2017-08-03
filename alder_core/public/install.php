<?php
    
    /*
     * All Rights Reserved.
     *
     * Copyright (c) 2016, Regrowth Studios Ltd.
     */
    
    // Delegate static file requests back to the PHP built-in webserver
    if (php_sapi_name() === 'cli-server'
        && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
    ) {
        return false;
    }

    // Require core global script.
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    // Require component-specific global scripts.
    foreach (glob(dirname(__DIR__) . DIRECTORY_SEPARATOR . "global" . DIRECTORY_SEPARATOR . "*.php") as $filename) {
        require_once $filename;
    }
    // Require core constants definitions file.
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "constants.php";
    // Require component-specific constants definition files.
    foreach (glob(file_build_path(dirname(__DIR__), "config", "constants", "*.php")) as $filename) {
        require_once $filename;
    }
