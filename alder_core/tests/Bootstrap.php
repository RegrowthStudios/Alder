<?php
    namespace AlderTest;
    
    error_reporting(E_ALL);
    
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
    
    require file_build_path(VENDOR_DIRECTORY, "autoload.php");
    
    // Create a config container.
    /** @var \Interop\Container\ContainerInterface $container */
    $container = require file_build_path(CONFIG_DIRECTORY, "container.php");
    
    // Set up the container holder.
    \Alder\DiContainer::set($container);
