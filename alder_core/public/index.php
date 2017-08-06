<?php
    
    /*
     * All Rights Reserved.
     *
     * Copyright (c) 2016, Regrowth Studios Ltd.
     */
    
    namespace Alder;
    
    use Alder\Stdlib\Timer;
    
    use Zend\Diactoros\ServerRequestFactory;
    use Zend\Expressive\Application;
    use Zend\Log\Logger;
    use Zend\Log\Writer\Stream as WriteStream;
    
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
    
    // Bootstrap application.
    try {
        // Time the request-to-response time if in development mode.
        if (ENV != PRODUCTION) {
            require_once file_build_path(ALDER_SRC_DIRECTORY, "Stdlib", "Timer.php");
            $timer = new Timer();
            $timer->start();
        }
        
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
        
        // Create a config container.
        /** @var \Interop\Container\ContainerInterface $container */
        $container = require file_build_path(CONFIG_DIRECTORY, "container.public.php");
        
        // Set up the container holder.
        DiContainer::set($container);
        
        // Initialise application.
        /** @var \Zend\Expressive\Application $app */
        $app = $container->get(Application::class);
        
        // Grab original request object.
        $request = ServerRequestFactory::fromGlobals();
        
        // Run app with grabbed request object.
        $app->run($request);
        
        // Store the resulting timing if in development mode.
        if (ENV != PRODUCTION) {
            $timer->stop();
            
            // Create timing file if it doesn't exist, then write contents of request and processing.
            $timingFileHandle = file_build_path(LOGS_DIRECTORY, "timings.json");
            $data = null;
            if (is_file($timingFileHandle)) {
                $data = json_decode(file_get_contents($timingFileHandle), true);
            } else {
                $data = ["time" => [], "uri" => [], "query" => [], "post" => [], "headers" => []];
            }
            $data["time"][] = $timer->getDuration();
            $data["uri"][] = $request->getUri()->getPath();
            $data["query"][] = $request->getUri()->getQuery();
            $data["post"][] = $request->getParsedBody();
            $data["header"][] = json_encode($request->getHeaders());
            file_put_contents($timingFileHandle, json_encode($data));
        }
    } catch (\Exception $ex) {
        // Log error if a critical exception occurred.
        critical_error($ex);
    }
