<?php

/*
 * All rights reserved.
 * 
 * Copyright (c) 2016, Regrowth Studios Ltd.
 */
    
    namespace Alder;
    
    use Alder\Stdlib\Timer;
    
    use Zend\Log\Logger;
    use Zend\Log\Writer\Stream as WriteStream;
    use Zend\Mvc\Application;
    
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . "global.php";
    require file_build_path(dirname(__DIR__), "config", "alder_pa.constants.php");
    
    // If in a debug mode, show errors.
    if (ENV != PRODUCTION) {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }
    
    // Try to bootsrap application and kick off execution.
    try {
        // Time the request to response time if not in production.
        if (ENV != PRODUCTION) {
            require file_build_path(SYCAMORE_MODULE_DIRECTORY, "src", "Sycamore", "Stdlib", "Timer.php");
            $timer = new Timer();
            $timer->start();
        }

        // Prepare autoloader.
        require file_build_path(VENDOR_DIRECTORY, "autoload.php");

        // Prepare error logger (use trigger_error to write via this logger).
        $errorStream = @fopen(file_build_path(LOGS_DIRECTORY, "errors.log"), "a");
        if (!$errorStream) {
            throw new \Exception("Failed to open error log file.");
        }
        $errorWriter = new WriteStream($errorStream);
        $errorLogger = new Logger();
        $errorLogger->addWriter($errorWriter);
        Logger::registerErrorHandler($errorLogger);
        Logger::registerExceptionHandler($errorLogger);

        // Initialise application.
        $application = Application::init(require file_build_path(CONFIG_DIRECTORY, "alder_pa.config.php"));
        $application->getServiceManager()->setService("ErrorLogger", $errorLogger);
        $request = $application->getRequest();
        $application->run();

        // Store the resulting timing if not in production.
        if (ENV != PRODUCTION) {
            // End timer.
            $timer->stop();

            // Create timings file if it doesn't exist, then write contents of request and time to respond to request.
            $timingFile = file_build_path(LOGS_DIRECTORY, "timings.json");
            $data = NULL;
            if (is_file($timingFile)) {
                $data = json_decode(file_get_contents($timingFile), true);
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
            $data["uri"][] = $request->getUriString();
            $data["query"][] = $request->getQuery()->toString();
            $data["post"][] = $request->getPost()->toString();
            $headers = preg_split("/\s\s+/",$request->getHeaders()->toString());
            if (empty(end($headers))) {
                array_pop($headers);
            }
            $data["header"][] = $headers;
            file_put_contents($timingFile, json_encode($data));
        }
    } catch (\Exception $ex) {
        // Log error if a critical exception occurred.
        error_log("/////  CRITICAL ERROR  \\\\\\\\\\" . PHP_EOL
                . "Error Code: " . $ex->getCode() . PHP_EOL
                . "Error Location: " . $ex->getFile() . " : " . $ex->getLine() . PHP_EOL
                . "Error Message: " . $ex->getMessage()) . PHP_EOL
                . "Stack Trace: " . PHP_EOL . $ex->getTraceAsString();
        exit();
    }
