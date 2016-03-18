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
    
    use Sycamore\Db\Row\MailMessage;
    use Sycamore\Mail\Exception\InvalidMessage;
    use Sycamore\Mail\Exception\InvalidSendTime;
    use Sycamore\Scheduler\Task\TaskInterface;
    use Sycamore\Scheduler\Task\Factory as TaskFactory;
    use Sycamore\Serialiser\API;
    use Sycamore\Serialiser\Object;
    use Sycamore\Stdlib\ArrayUtils;
    
    use Zend\Mail\Transport\Factory as TransportFactory;
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Handles the scheduling of emails to be sent by the server, as well as the construction and dispatch of those emails.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Mailer
    {
        /**
         * Parameter to signal no delay on initial scheduling.
         */
        const NOW = "now";
        
        /**
         * Transport used for sending emails.
         * 
         * @var \Zend\Mail\Transport\TransportInterface 
         */
        protected $transport;
        
        /**
         * The service manager for this application instance.
         * 
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;
        
        /**
         * Prepares the mailer by constructing its transport as per the application configuration dictates.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager, $config = NULL)
        {
            $emailConf = (!is_array($config) ? $serviceManager->get("Config")["Sycamore"]["email"] : $config);

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
            $this->serviceManager = $serviceManager;
        }
        
        /**
         * Schedules the sending of a message via the mailer's transport.
         * 
         * @param array|\Traversable $message
         * @param string $purpose
         * @param array|\Traversable|string $sendTime
         * @param int $creationTime
         */
        public function scheduleMessage($message, $purpose = "", $sendTime = self::NOW, $creationTime = NULL)
        {
            // Validate message.
            try {
                $validatedMessage = ArrayUtils::validateArrayLike($message, get_class($this), true);
            } catch (\InvalidArgumentException $ex) {
                throw new InvalidMessage("Message is exptected to be an associative array or .");
            }
            
            if ($sendTime === self::NOW) {
                $this->sendMessage($validatedMessage);
            } else {
                // Get send time timestamp, failing if the send time is invalid.
                if (is_array($sendTime)) {
                    $sendTimeArray = $sendTime;
                } else if (is_string($sendTime)) {
                    $sendTimeArray = getdate($sendTime);
                } else {
                    throw new InvalidSendTime("Delay is expected to be a time-formatted string.");
                }
                if (!$sendTimeArray) {
                    throw new InvalidSendTime("Delay provided was invalid date.");
                }
                
                // Get current timestamp.
                $time = time();
                
                // Prepare the new mail message.
                $mailMessage = new MailMessage();
                $mailMessage->serialisedMessage = Object::encode($validatedMessage);
                $mailMessage->sendTime = API::encode($sendTimeArray);
                $mailMessage->purpose = (string) $purpose;
                $mailMessage->sent = 0;
                $mailMessage->cancelled = 0;
                $mailMessage->lastUpdateTime = ((int) $creationTime) ?: $time;
                //$mailMessage->lastUpdatorId = Visitor::getInstance()->id;
                $mailMessage->creationTime = ((int) $creationTime) ?: $time;
                //$mailMessage->creatorId = Visitor::getInstance()->id;
                
                
                // Grab the mail message table.
                $tableCache = $this->serviceManager->get("SycamoreTableCache");
                $mailMessageTable = $tableCache->fetchTable("MailMessage");
                
                // Save Message to database and get ID.
                $mailMessageTable->insert($mailMessage);
                $mailMessage->id = $mailMessageTable->lastInsertValue();
                
                // Create a new task.
                $taskDetails = array();
                $taskDetails["job"] = "php " . APP_DIRECTORY . "/public/index.php email $mailMessage->id";
                $taskDetails["executiveDate"] = [
                    "year" => $sendTimeArray["year"],
                    "month" => $sendTimeArray["month"],
                    "day" => $sendTimeArray["day"],
                ];
                $taskDetails["executiveTime"] = [
                    "hour" => $sendTimeArray["hours"],
                    "minutes" => $sendTimeArray["minutes"],
                    "seconds" => $sendTimeArray["seconds"],
                ];
                $taskDetails["scheduleType"] = TaskInterface::SCHEDULE_ONCE;
                $task = TaskFactory::create($taskDetails);
                
                // Update mail message with task object for deleting purposes.
                $mailMessage->task = Object::encode($task);
                $mailMessageTable->saveById($mailMessage, $mailMessage->id);
                
                // Schedule the task.
                $scheduler = $this->serviceManager->get("Scheduler");
                $scheduler->addTask($task);
            }
        }
    }
