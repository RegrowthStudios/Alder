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

    use Sycamore\Application;
    use Sycamore\Autoloader;
    use Sycamore\FrontController;
    use Sycamore\Utils\Timer;
    
    use Zend\Http\PhpEnvironment\Request;
    
    class SycamoreIndex
    {
        /**
         * Initialises the Sycamore autoloader, application and front controller.
         */
        public static function run($appDir)
        {
            define("APP_DIRECTORY", $appDir);
            define("LIBRARY_DIRECTORY", APP_DIRECTORY."/library");
            define("CONFIG_DIRECTORY", APP_DIRECTORY."/conf");
            define("SYCAMORE_DIRECTORY", LIBRARY_DIRECTORY."/Sycamore");
            
            try {    
                require(SYCAMORE_DIRECTORY . "/Utils/Timer.php");
                $timer = new Timer();
                $timer->begin();

                require(SYCAMORE_DIRECTORY . "/Autoloader.php");
                Autoloader::getInstance()->setupAutoloader();

                Application::initialise();

                $request = new Request(false);

                $frontController = new FrontController();
                $frontController->run($request);

                $timer->end();
                file_put_contents(APP_DIRECTORY . "/logs/timings.txt", "Process Time: ".$timer->getDuration()."s  -  Request: $page\n", FILE_APPEND);
            } catch (Exception $ex) {
                self::logCriticalError($ex);
                exit();
            }
        }
    
        /**
         * Quick critical error logging.
         * 
         * @param \Exception $ex
         */
        protected static function logCriticalError(\Exception $ex) {
            error_log("/////  CRITICAL ERROR  \\\\\\\\\\" . PHP_EOL 
                    . "Error Code: " . $ex->getCode() . PHP_EOL 
                    . "Error Location: " . $ex->getFile() . " : " . $ex->getLine() . PHP_EOL 
                    . "Error Message: " . $ex->getMessage()) . PHP_EOL
                    . "Stack Trace: " . PHP_EOL . $ex->getTraceAsString();
        }
        
        protected function __construct()
        {            
        }
    }