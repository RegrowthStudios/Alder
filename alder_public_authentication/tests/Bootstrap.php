<?php
    namespace AlderTest;
    
    error_reporting(E_ALL | E_STRICT);
    
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    require file_build_path(dirname(__DIR__), "config", "constants.php");
    
    require file_build_path(VENDOR_DIRECTORY, "autoload.php");
