<?php
    
    /*
     * All Rights Reserved.
     *
     * Copyright (c) 2016, Regrowth Studios Ltd.
     */
    
    namespace Alder;
    
    use \Zend\Diactoros\ServerRequestFactory;
    use \Zend\Expressive\Application;
    use \Zend\Log\Logger;
    use \Zend\Log\Writer\Stream as WriteStream;

    // Delegate static file requests back to the PHP built-in webserver
    if (php_sapi_name() === 'cli-server'
        && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
    ) {
        return false;
    }

    // Require core global script.
    require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    // Require component-specific global scripts.
    foreach (glob(file_build_path(dirname(__DIR__), "global", "*.php")) as $filename) {
        require_once $filename;
    }
    // Require core constants definitions file.
    require_once file_build_path(dirname(__DIR__), "config", "constants.php");
    // Require component-specific constants definition files.
    foreach (glob(file_build_path(CONFIG_DIRECTORY, "constants", "*.php")) as $filename) {
        require_once $filename;
    }

    try {
        // Prepare autoloader.
        require_once file_build_path(VENDOR_DIRECTORY, "autoload.php");
        
        // Prepare error logger.
        $errorStream = @fopen(file_build_path(LOGS_DIRECTORY, "errors.log"), "a");
        if (!$errorStream) {
            throw new \Exception("Failed to open error log file.");
        }
        $errorWriter = new WriteStream($errorStream);
        $errorLogger = new Logger();
        $errorLogger->addWriter($errorWriter);
        Logger::registerErrorHandler($errorLogger);
        Logger::registerExceptionHandler($errorLogger);
        
        // TODO(Matthew): Add middleware that checks if we're installed, and if not serve a page prompting install.

        // Create a config container.
        /** @var \Interop\Container\ContainerInterface $container */
        $container = require file_build_path(CONFIG_DIRECTORY, "container.admin.php");
        
        // Set up the container holder.
        DiContainer::set($container);
        
        // Initialise application.
        /** @var \Zend\Expressive\Application $app */
        $app = $container->get(Application::class);
        
        // Grab original request object.
        $request = ServerRequestFactory::fromGlobals();
        
        // Run app with grabbed request object.
        $app->run($request);
    } catch (\Exception $ex) {
        // Log error if a critical exception occurred.
        critical_error($ex);
    }
