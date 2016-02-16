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

    namespace Sycamore\Mail;
    
    use Sycamore\Application;
    use Sycamore\Visitor;
    use Sycamore\Attachment\AttachmentManager;
    use Sycamore\Cron\Job;
    use Sycamore\Cron\Scheduler;
    use Sycamore\Row\MailMessage;
    use Sycamore\Utils\ObjectData;
    use Sycamore\Utils\TableCache;
    
    use Zend\Mail\Transport\Factory as TransportFactory;
    
    /**
     * Singleton Mailer.
     */
    class Mailer
    {
        /**
         * Parameter to signal no delay on initial scheduling.
         */
        const NO_DELAY = "none";
        
        /**
         * Singleton insance of Mailer.
         *
         * @var \Sycamore\Mail\Mailer
         */
        protected static $instance;
        
        /**
         * Transport used for sending emails.
         * 
         * @var \Zend\Mail\Transport\TransportInterface 
         */
        protected $transport;
        
        /**
         * Sends a message via the mailer's transport.
         * 
         * @param \Sycamore\Mail\Message $message
         * @param string $purpose
         * @param string $delayTo
         * @param int $creationTime
         */
        public function scheduleMessage(\Sycamore\Mail\MessageDataPacket $message, $purpose, $delayTo = self::NO_DELAY, $creationTime = NULL)
        {
            if ($delayTo === self::NO_DELAY) {
                $this->sendMessage($message);
            } else {
                if (!is_string($delayTo)) {
                    throw new \InvalidArgumentException("Delay is expected to be a string.");
                }
                
                // Get timestamp
                $timestamp = strtotime($delayTo);
                if (!$timestamp) {
                    throw new \InvalidArgumentException("Delay provided was invalid date.");
                }
                
                // Get current timestamp.
                $time = time();
                
                // Grab the mail message table.
                $mailMessageTable = TableCache::getTableFromCache("MailMessage");
                
                // Prepare the new mail message.
                $mailMessage = new MailMessage();
                $mailMessage->serialisedMessage = ObjectData::encode($message);
                $mailMessage->sendTime = $timestamp;
                $mailMessage->purpose = (is_string($purpose) ? $purpose : "");
                $mailMessage->sent = 0;
                $mailMessage->cancelled = 0;
                $mailMessage->lastUpdateTime = (is_int($creationTime) ? $creationTime : $time);
                $mailMessage->lastUpdatorId = Visitor::getInstance()->id;
                $mailMessage->creationTime = (is_int($creationTime) ? $creationTime : $time);
                $mailMessage->creatorId = Visitor::getInstance()->id;
                
                // Save Message to database and get ID.
                $mailMessageTable->saveById($mailMessage);
                $mailMessage->id = $mailMessageTable->lastInsertValue();
                
                // Create a new cron job.
                $task = new Job();
                $task->setTask("php " . APP_DIRECTORY . "/public/index.php email $mailMessage->id");
                $task->setWhenUtc($timestamp);
                
                // Update mail message with cron job string for deleting purposes.
                $mailMessage->cronJob = $task->getJob();
                $mailMessageTable->saveById($mailMessage, $mailMessage->id);
                
                // Schedule the cron job.
                Scheduler::getInstance()->addCronJobs($task);
            }
        }
        
        /**
         * Update the time at which to send a message.
         * 
         * @param \Sycamore\Row\MailMessage $mailMessage
         * @param bool $sendNow
         * 
         * @throws \InvalidArgumentException
         */
        public function updateMessageSendTime(\Sycamore\Row\MailMessage& $mailMessage, $sendNow = false)
        {
            if ($sendNow == self::NO_DELAY) {
                $this->sendMessage(ObjectData::decode($mailMessage->serialisedMessage));
            } else {
                // Remove old cron job.
                Scheduler::getInstance()->removeCronJobs("/" . $mailMessage->cronJob . "/");
                
                // Create a new cron job.
                $task = new Job();
                $task->setTask("php " . APP_DIRECTORY . "/public/index.php email $mailMessage->id");
                $task->setWhenUtc($mailMessage->sendTime);
                
                // Schedule the new cron job.
                Scheduler::getInstance()->addCronJobs($task);
            }
        }
        
        /**
         * Stop the sending of a message.
         * 
         * @param \Sycamore\Row\MailMessage $mailMessage
         */
        public function stopMessageSend(\Sycamore\Row\MailMessage $mailMessage, $permanent = false)
        {
            // Remove cron job.
            Scheduler::getInstance()->removeCronJobs("/" . $mailMessage->cronJob . "/");
            
            // Delete attachments of message if stop is permanent.
            if ($permanent) {
                // Deserialise message data packet.
                $message = ObjectData::decode($mailMessage->serialisedMessage);
                
                // Grab table.
                $attachmentTable = TableCache::getTableFromCache("Attachment");
                
                // Delete each attachment in turn.
                foreach ($message->getAttachments as $attachmentId) {
                    $attachment = $attachmentTable->getById($attachmentId);
                    AttachmentManager::removeAttachment($attachment);
                }
            }
        }
        
        public function sendMessage(\Sycamore\Mail\MessageDataPacket $message)
        {
            // TODO(Matthew): Implement. [Calls prepareMessage then sends the resulting (\Zend|\Sycamore)\Mail\Messages.]
        }
        
        public function prepareMessage(\Sycamore\Mail\MessageDataPacket $message)
        {
            /* Global Preparation Section */
            
            // TODO(Matthew): Create a class that can parse a string and add in global parameters. Utilise Zend\I18n
            
            /* Per Recipient Preparation Section */
            
            // Get recipient groups for the message.
            $recipientGroups = $message->getRecipientGroups();
            
            // Fetch appropriate tables.
            $recipientTable = TableCache::getTableFromCache($message->getType()["recipientTable"]);
            $recipientGroupMapsTable = TableCache::getTableFromCache($message->getType()["recipientGroupMapsTable"]);
            
            // Get recipients.
            $recipients = NULL;
            if (empty($recipientGroups)) {
                $recipients = $recipientTable->fetchAll();
            } else {
                foreach ($recipientGroups as $recipientGroupId) {
                    $recipientIds = $recipientGroupMapsTable->getByGroupId($recipientGroupId);
                    foreach ($recipientIds as $recipientId) {
                        $recipients[] = $recipientTable->getById($recipientId);
                    }
                }
            }
        }
        
        /**
         * Protected constructor. Use {@link getInstance()} instead.
         */
        protected function __construct()
        {
            $this->prepareMailer();
        }
        
        /**
         * Construct the transport for the mailer.
         */
        protected function prepareMailer()
        {
            $cacheManager = new DataCache();
            $cacheManager->initialise("mailer", "transport");

            $cachedResult = $cacheManager->getCachedData();
			
            if (!$cachedResult) {
                $emailConf = Application::getConfig()->email;

                $spec = array();
                $spec["type"] = strtolower($emailConf->transport);
                if ($spec["type"] == "smtp" || $spec["type"] == "file") {
                    $optionsConf = $emailConf->options;
                    $connConf = $optionsConf->connection;

                    $spec["options"] = array();
                    $spec["options"]["name"] = $optionsConf->name;
                    $spec["options"]["host"] = $optionsConf->host;
                    $spec["options"]["port"] = $optionsConf->port;
                    $spec["options"]["connection_class"] = $connConf->class;

                    $spec["options"]["connection_config"] = array();
                    $spec["options"]["connection_config"]["username"] = $connConf->username;
                    $spec["options"]["connection_config"]["password"] = $connConf->password;
                    if (!empty($connConf->ssl)) {
                        $spec["options"]["connection_config"]["ssl"] = $connConf->ssl;
                    }
                }

                $this->transport = TransportFactory::create($spec);

                $cacheManager->setCachedData($this->transport);
            } else {
                $this->transport = $cachedResult;
            }
        }
        
        /**
        * Gets the mailer instance.
        *
        * @return \Sycamore\Mail\Mailer
        */
        public static final function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }
    }