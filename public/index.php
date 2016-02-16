<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    namespace Sycamore;

    use Sycamore\FrontController;
    use Sycamore\Utils\Timer;
    
    use Zend\Http\PhpEnvironment\Request;
    use Zend\Mvc\Application;
    
    // Define a bunch of directory constants.
    define("APP_DIRECTORY", dirname(__DIR__));
    define("MODULES_DIRECTORY", APP_DIRECTORY."/modules");
    define("LIBRARY_DIRECTORY", APP_DIRECTORY."/library");
    define("CONFIG_DIRECTORY", APP_DIRECTORY."/config");
    define("SYCAMORE_DIRECTORY", LIBRARY_DIRECTORY."/Sycamore");
    
    // Define possible ENV states.
    define("PRODUCTION", "production");
    define("TESTING", "testing");
    define("DEVELOPMENT", "development");
    
    // Define ENV - the environment state (development or production).
    // ENV state stored in a PHP file to get around Nginx not supporting setting environment variables a la Apache.
    define("ENV", require (CONFIG_DIRECTORY . "/env.state.php"));

    // If in a debug mode, show errors.
    if (ENV != PRODUCTION) {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
    }

    // Try to bootsrap application and kick off execution.
    try {
        if (ENV != PRODUCTION) {
            // Get and begin timer.
            require (SYCAMORE_DIRECTORY . "/Utils/Timer.php");
            $timer = new Timer();
            $timer->begin();
        }

        // Prepare autoloader.
        require (SYCAMORE_DIRECTORY . "/autoload_register.php");

        // Initialise application.
        Application::init(require (CONFIG_DIRECTORY . "/sycamore.config.php"))->run();

        // Initialise application.
        //Application::initialise();

        // Construct request object.
        $request = new Request(false);

        // Run front controller, executing appropriate controller action.
        $frontController = new FrontController();
        $frontController->run($request);

        if (ENV != PRODUCTION) {
            // End timer.
            $timer->end();

            // Create timings file if it doesn't exist, then write contents of request and time to respond to request.
            $timingFile = APP_DIRECTORY . "/logs/timings.csv";
            if (!is_file($timingFile)) {
                file_put_contents($timingFile, "Processing Times, Request URI, Request GET, Request POST, Request Headers");
            }
            file_put_contents($timingFile, "{$timer->getDuration()}, {$request->getUriString()}, {$request->getQuery()->toString()}, {$request->getPost()->toString()}, {$request->getHeaders()->toString()}", FILE_APPEND);
        }
    } catch (Exception $ex) {
        error_log("/////  CRITICAL ERROR  \\\\\\\\\\" . PHP_EOL 
                . "Error Code: " . $ex->getCode() . PHP_EOL 
                . "Error Location: " . $ex->getFile() . " : " . $ex->getLine() . PHP_EOL 
                . "Error Message: " . $ex->getMessage()) . PHP_EOL
                . "Stack Trace: " . PHP_EOL . $ex->getTraceAsString();
        exit();
    }
            