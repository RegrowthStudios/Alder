<?php
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
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
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
         * @param array|\Traversable $message The message to be sent.
         * @param string $purpose The purpose of the message, e.g. "newsletter".
         * @param array|string $sendTime The time to send the message at.
         * @param int $creationTime Optional variable to set a custom creation time of the message.
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
                // Fill in missing data with current date and times.
                $sendTimeArray = array_merge($sendTimeArray, getdate());
                
                // Get current timestamp.
                $time = time();
                
                // Fetch visitor ID.
                $visitorId = $this->serviceManager->get("Sycamore\Visitor")->get("id");
                
                // Prepare the new mail message.
                $mailMessage = new MailMessage();
                $mailMessage->serialisedMessage = API::encode($validatedMessage);
                $mailMessage->sendTime = mktime(
                                            $sendTimeArray["hours"],
                                            $sendTimeArray["minutes"],
                                            $sendTimeArray["seconds"],
                                            $sendTimeArray["month"],
                                            $sendTimeArray["day"],
                                            $sendTimeArray["year"]
                                        );
                $mailMessage->purpose = (string) $purpose;
                $mailMessage->sent = 0;
                $mailMessage->cancelled = 0;
                $mailMessage->lastUpdateTime = ((int) $creationTime) ?: $time;
                $mailMessage->lastUpdatorId = $visitorId;
                $mailMessage->creationTime = ((int) $creationTime) ?: $time;
                $mailMessage->creatorId = $visitorId;
                
                
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
        
        /**
         * Updates the send time of an existing mail message.
         * 
         * @param int|\Sycamore\Db\Row\MailMessage $mailMessage The mail message to update the send time of.
         * @param array|string $sendTime The new time to send the message at.
         * @param bool $force Whether to force the update if the message was previously cancelled.
         * @param int $updatorId The ID of the updator, if NULL, visitor ID used.
         * 
         * @return bool True on successful update, false otherwise.
         * 
         * @throws \InvalidArgumentException if $mailMessage is not a valid ID or \Sycamore\Db\Row\MailMessage object.
         */
        public function updateMessageSendTime($mailMessage, $sendTime, $force = false, $updatorId = NULL)
        {
            // Grab mail message table.
            $tableCache = $this->serviceManager->get("SycamoreTableCache");
            $mailMessageTable = $tableCache->fetchTable("MailMessage");
            
            // Get mail message object if ID provided.
            if (is_numeric($mailMessage)) {
                $mailMessage = $mailMessageTable->getById((int) $mailMessage);
            } else if (!$mailMessage instanceof MailMessage) {
                throw new \InvalidArgumentException("Must provide either a mail message ID or object into mailMessage.");
            }
            
            if ($mailMessage->cancelled && !$force) {
                return false;
            }
            
            if ($sendTime == self::NOW) {
                // Send message.
                $this->sendMessage(API::decode($mailMessage->serialisedMessage));
                
                // Remove redundant task.
                $scheduler = $this->serviceManager->get("Scheduler");
                $scheduler->removeTask(Object::decode($mailMessage->task));
                
                // Update mail message entry.
                $mailMessage->sendTime = time();
                $mailMessage->sent = true;
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
                // Fill in missing data with current date and times.
                $sendTimeArray = array_merge($sendTimeArray, getdate());
                
                // Update mail message send time.
                $mailMessage->sendTime = mktime(
                                            $sendTimeArray["hours"],
                                            $sendTimeArray["minutes"],
                                            $sendTimeArray["seconds"],
                                            $sendTimeArray["month"],
                                            $sendTimeArray["day"],
                                            $sendTimeArray["year"]
                                        );
                
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
                
                // Remove old task and add new one.
                $scheduler = $this->serviceManager->get("Scheduler");
                $scheduler->removeTask(Object::decode($mailMessage->task));
                $scheduler->addTask($task);
            }
            
            // Update mail message entry with common data.
            $mailMessage->cancelled = false;
            $mailMessage->lastUpdateTime = time();
            $mailMessage->lastUpdatorId = $updatorId ?: $this->serviceManager->get("Sycamore\Visitor")->get("id");
            
            // Save changes to mail message.
            $mailMessageTable->save($mailMessage, $mailMessage->id);
        }
        
        // Todo(Matthew): Unlink attachments associated with the cancelled message if permanent.
        /**
         * Cancels the sending of a scheduled message.
         * 
         * @param int|\Sycamore\Db\Row\MailMessage $mailMessage The mail message to cancel the sending of.
         * @param bool $permanent Whether the cancellation is permanent.
         * @param int $updatorId The ID of the updator, if NULL, visitor ID used.
         * 
         * @return bool True on successful cancellation, false otherwise.
         * 
         * @throws \InvalidArgumentException if $mailMessage is not a valid ID or \Sycamore\Db\Row\MailMessage object.
         */
        public function stopMessageSend($mailMessage, $permanent = false, $updatorId = NULL)
        {
            // Grab mail message table.
            $tableCache = $this->serviceManager->get("SycamoreTableCache");
            $mailMessageTable = $tableCache->fetchTable("MailMessage");
            
            // Get mail message object if ID provided.
            if (is_numeric($mailMessage)) {
                $mailMessage = $mailMessageTable->getById((int) $mailMessage);
            } else if (!$mailMessage instanceof MailMessage) {
                throw new \InvalidArgumentException("Must provide either a mail message ID or object into mailMessage.");
            }
            
            if ($mailMessage->cancelled && !$permanent) {
                return false;
            }
            
            // Grab scheduler and remove task.
            $scheduler = $this->serviceManager->get("Scheduler");
            $scheduler->removeTask(Object::decode($mailMessage->task));
            
            // Delete table entry if permanent, otherwise update row with data.
            if ($permanent) {
                $mailMessageTable->deleteById($mailMessage->id);
            } else {
                $mailMessage->cancelled = true;
                $mailMessage->lastUpdateTime = time();
                $mailMessage->lastUpdatorId = $updatorId ?: $this->serviceManager->get("Sycamore\Visitor")->get("id");
                $mailMessageTable->save($mailMessage, $mailMessage->id);
            }
            
            return true;
        }
    }
