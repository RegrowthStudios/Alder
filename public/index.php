<?php

/*
 * All rights reserved.
 * 
 * Copyright (c) 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 */

    namespace Sycamore;

    use Sycamore\Stdlib\Timer;

    use Zend\Log\Logger;
    use Zend\Log\Writer\Stream as WriteStream;
    use Zend\Mvc\Application;

    // Define a bunch of directory constants.
    define("APP_DIRECTORY", dirname(__DIR__));
    define("MODULES_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."modules");
    define("VENDOR_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."vendor");
    define("CONFIG_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."config");
    define("LOGS_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."logs");
    define("TEMP_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."temp");
    define("CACHE_DIRECTORY", APP_DIRECTORY.DIRECTORY_SEPARATOR."cache");
    define("SYCAMORE_MODULE_DIRECTORY", MODULES_DIRECTORY."Sycamore");

    // Define possible ENV states.
    define("PRODUCTION", "production");
    define("TESTING", "testing");
    define("DEVELOPMENT", "development");

    // Define ENV - the environment state (development or production).
    // ENV state stored in a PHP file to get around Nginx not supporting setting environment variables a la Apache.
    define("ENV", require CONFIG_DIRECTORY."/env.state.php");

    // Define value to set config values to that MUST be overridden by a given installation.
    define("DEFAULT_VAL", "CHANGE");

    // Define appropriate OS constant.
    define("UNIX", "Unix");
    define("WINDOWS", "Windows");
    switch (php_uname("s")) {
        // Treat FreeBSD and Linux as basically UNIX because they'll be treated the same as UNIX OS's anyway.
        case "FreeBSD":
        case "Linux":
        case "Solaris":
            define("OS", UNIX);
            break;
        case "Windows NT":
            define("OS", WINDOWS);
            break;
    }

    // If in a debug mode, show errors.
    if (ENV != PRODUCTION) {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }

    // Try to bootsrap application and kick off execution.
    try {
        // Time the request to response time if not in production.
        if (ENV != PRODUCTION) {
            // Get and begin timer.
            require (SYCAMORE_MODULE_DIRECTORY . "/src/Sycamore/Stdlib/Timer.php");
            $timer = new Timer();
            $timer->start();
        }

        // Prepare autoloader.
        require (VENDOR_DIRECTORY . "/autoload.php");

        // Prepare error logger (use trigger_error to write via this logger).
        $errorStream = @fopen(LOGS_DIRECTORY."/errors.log", "a");
        if (!$errorStream) {
            throw new \Exception("Failed to open error log file.");
        }
        $errorWriter = new WriteStream($errorStream);
        $errorLogger = new Logger();
        $errorLogger->addWriter($errorWriter);
        Logger::registerErrorHandler($errorLogger);
        Logger::registerExceptionHandler($errorLogger);

        // Initialise application.
        $application = Application::init(require (CONFIG_DIRECTORY . "/sycamore.config.php"));
        $application->getServiceManager()->setService("ErrorLogger", $errorLogger);
        $request = $application->getRequest();
        $application->run();

        // Store the resulting timing if not in production.
        if (ENV != PRODUCTION) {
            // End timer.
            $timer->stop();

            // Create timings file if it doesn't exist, then write contents of request and time to respond to request.
            $timingFile = LOGS_DIRECTORY."/timings.json";
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
