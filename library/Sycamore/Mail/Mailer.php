<?php

/**
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
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
 *
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License 3.0
 */

    namespace Sycamore\Mail;
    
    use Zend\Mail\Transport\Factory as TransportFactory;
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    class Mailer
    {
        /**
         * Parameter to signal no delay on initial scheduling.
         */
        const NO_DELAY = "none";
        
        /**
         * Transport used for sending emails.
         * 
         * @var \Zend\Mail\Transport\TransportInterface 
         */
        protected $transport;
        
        /**
         * Prepares the mailer by constructing its transport as per the application configuration dictates.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            $emailConf = $serviceManager->get("Config")["Sycamore"]["email"];

            $spec = array();
            $spec["type"] = strtolower($emailConf["transport"]);
            if ($spec["type"] == "smtp" || $spec["type"] == "file") {
                $optionsConf = $emailConf["options"];
                $connConf = $optionsConf["connection"];

                $spec["options"] = array();
                $spec["options"]["name"] = $optionsConf["name"];
                $spec["options"]["host"] = $optionsConf["host"];
                $spec["options"]["port"] = $optionsConf["port"];
                $spec["options"]["connection_class"] = $connConf["class"];

                $spec["options"]["connection_config"] = array();
                $spec["options"]["connection_config"]["username"] = $connConf["username"];
                $spec["options"]["connection_config"]["password"] = $connConf["password"];
                if (!empty($connConf["ssl"])) {
                    $spec["options"]["connection_config"]["ssl"] = $connConf["ssl"];
                }
            }

            $this->transport = TransportFactory::create($spec);
        }
    }
    