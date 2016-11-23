<?php
    namespace AlderTest;
    
    error_reporting(E_ALL | E_STRICT);

    // Require global scripts.
    foreach (glob(dirname(__DIR__) . DIRECTORY_SEPARATOR . "global" . DIRECTORY_SEPARATOR . "*.php") as $filename) {
        require_once $filename;
    }
    // Require constants definition files.
    foreach (glob(file_build_path(dirname(__DIR__), "config", "constants", "*.php")) as $filename) {
        require_once $filename;
    }
    
    require file_build_path(VENDOR_DIRECTORY, "autoload.php");
