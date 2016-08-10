<?php

/*
 * All Rights Reserved.
 * 
 * Copyright (c) 2016, Regrowth Studios Ltd.
 */

    namespace Alder;
    
    use Alder\Container;
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
    
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    require file_build_path(dirname(__DIR__), "config", "contants.php");
    
    // Bootstrap application.
    try {
        // Time the request-to-response time if in development mode.
        if (ENV != PRODUCTION) {
            require file_build_path(ALDER_SRC_DIRECTORY, "Stdlib", "Timer.php");
            $timer = new Timer();
            $timer->start();
        }
        
        // Prepare autoloader.
        require file_build_path(VENDOR_DIRECTORY, "autoload.php");
        
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
        $container = require file_build_path(CONFIG_DIRECTORY, "container.php");

        // Set up the container holder.
        Container::set($container);
        
        // Initialise application.
        /** @var \Zend\Expressive\Application $app */
        $app = $container->get(Application::class);
        $request = ServerRequestFactory::fromGlobals();
        $app->run($request);
        
        // Store the resulting timing if in development mode.
        if (ENV != PRODUCTION) {
            $timer->stop();
            
            // Create timing file if it doesn't exist, then write contents of request and processing.
            $timingFileHandle = file_build_path(LOGS_DIRECTORY, "timings.json");
            $data = NULL;
            if (is_file($timingFileHandle)) {
                $data = json_decode(file_get_contents($timingFileHandle), true);
            } else {
                $data = [
                    "time" => [],
                    "uri" => [],
                    "query" => [],
                    "post" => [],
                    "headers" => []
                ];
            }
            $data["time"][] = $timer->getDuration();
            $data["uri"][] = $request->getUri()->getPath();
            $data["query"][] = $request->getUri()->getQuery();
            $data["post"][] = $request->getParsedBody();
            $data["header"][] = json_encode($request->getHeaders());
            file_put_contents($timingFile, json_encode($data));
        }
    } catch (Exception $ex) {
        // Log error if a critical exception occurred.
        error_log("/////  CRITICAL ERROR  \\\\\\\\\\" . PHP_EOL
                . "Error Code: " . $ex->getCode() . PHP_EOL
                . "Error Location: " . $ex->getFile() . " : " . $ex->getLine() . PHP_EOL
                . "Error Message: " . $ex->getMessage()) . PHP_EOL
                . "Stack Trace: " . PHP_EOL . $ex->getTraceAsString();
        exit();
    }
