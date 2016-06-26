<?php

    namespace AlderTest;

    error_reporting(E_ALL | E_STRICT);
    chdir(__DIR__);
    
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    require file_build_path(dirname(__DIR__), "config", "alder_pa.constants.php");
    
    require "./Bootstrap.php";
    require "./TestHelpers.php";
    
    Bootstrap::init();
    Bootstrap::chroot();