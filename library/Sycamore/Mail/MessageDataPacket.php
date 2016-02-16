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
    
    class MessageDataPacket
    {
        /**
         * Stores the type of message.
         *
         * @var string
         */
        protected $type;
        
        /**
         * The ID of the sender.
         *
         * @var int
         */
        protected $sender;
        
        /**
         * The collection of IDs of the recipient groups.
         *
         * @var array
         */
        protected $recipientGroups = array();
        
        /**
         * The subject of the message.
         *
         * @var string
         */
        protected $subject;
        
        /**
         * The collection of body blocks of the message.
         * 
         * @var array
         */
        protected $bodyBlocks = array();
        
        /**
         * The collection of attachments associated with the message.
         *
         * @var array
         */
        protected $attachments = array();
        
        public function __construct($messageType)
        {
            if (!is_string($messageType)) {
                throw new \InvalidArgumentException("Recipient type must be a string.");
            } else if (!in_array(strtoupper($messageType), array_keys(Message::TYPES))) {
                throw new \InvalidArgumentException("Recipient type must be one of those defined in Recipient::TYPES.");
            }
            $this->type = strtoupper($messageType);
        }
        
        public function setSender($senderId)
        {
            if (!is_numeric($senderId)) {
                throw new \InvalidArgumentException("Sender ID must be numeric.");
            }
            $this->sender = $senderId;
            
            return $this;
        }
        
        public function addRecipientGroup($recipientGroupId)
        {
            if (!is_numeric($recipientGroupId)) {
                throw new \InvalidArgumentException("Recipient group ID must be numeric.");
            }
            $this->recipients[] = (int) $recipientGroupId;
            
            return $this;
        }
        
        public function addRecpients($recipientGroupIds)
        {
            if (!is_array($recipientGroupIds) || $recipientGroupIds instanceof \Traversable) {
                throw new \InvalidArgumentException("Recipient group IDs must be traversable.");
            }
            try {
                foreach ($recipientGroupIds as $recipientGroupId) {
                    $this->addRecipient($recipientGroupId);
                }
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            
            return $this;
        }
        
        public function setSubject($subject)
        {
            if (!is_string($subject)) {
                throw new \InvalidArgumentException("Subject must be a string.");
            }
            $this->subject = $subject;
            
            return $this;
        }
        
        public function addBodyBlock($bodyBlock)
        {
            if (!is_array($bodyBlock) || count($bodyBlock) != 2 || !is_string($bodyBlock[0]) || !is_string($bodyBlock[1])) {
                throw new \InvalidArgumentException("Body block provided must be an array containing a (string) template name and (string) content.");
            }
            $this->bodyBlocks[] = $bodyBlock;
            
            return $this;
        }
        
        public function addBodyBlocks($bodyBlocks)
        {
            if (!is_array($bodyBlocks) || $bodyBlocks instanceof \Traversable) {
                throw new \InvalidArgumentException("Body blocks must be traversable.");
            }
            try {
                foreach ($bodyBlocks as $bodyBlock) {
                    $this->addBodyBlock($bodyBlock);
                }
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            
            return $this;
        }
        
        public function addAttachment($attachmentId)
        {
            if (!is_numeric($attachmentId)) {
                throw new \InvalidArgumentException("Attachment ID must be numeric.");
            }
            $this->attachments[] = (int) $attachmentId;
            
            return $this;
        }
        
        public function addAttachments($attachmentIds)
        {
            if (!is_array($attachmentIds) || $attachmentIds instanceof \Traversable) {
                throw new \InvalidArgumentException("Attachment IDs must be traversable.");
            }
            try {
                foreach ($attachmentIds as $attachmentId) {
                    $this->addAttachment($attachmentId);
                }
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            
            return $this;
        }
        
        public function getType()
        {
            return $this->type;
        }
        
        public function getSender()
        {
            return $this->sender;
        }
        
        public function getRecipientGroups()
        {
            return $this->recipientGroups;
        }
        
        public function getSubject()
        {
            return $this->subject;
        }
        
        public function getBodyBlocks()
        {
            return $this->bodyBlocks;
        }
        
        public function getAttachments()
        {
            return $this->attachments;
        }
    }