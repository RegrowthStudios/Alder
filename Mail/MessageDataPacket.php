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
        protected $type;
        
        protected $sender;
        
        protected $recipients = array();
        
        protected $subject;
        
        protected $bodyBlocks;
        
        public function __construct($messageType)
        {
            if (!is_string($messageType)) {
                throw new \InvalidArgumentException("Recipient type must be a string.");
            } else if (!in_array(strtoupper($messageType), array_keys(Message::TYPES))) {
                throw new \InvalidArgumentException("Recipient type must be one of those defined in Recipient::recipientTypes.");
            }
            $this->type = strtoupper($messageType);
        }
        
        public function setSender($sender)
        {
            if (!filter_var($sender, FILTER_SANITIZE_EMAIL)) {
                throw new \InvalidArgumentException("Sender must be an email.");
            }
            $this->sender = $sender;
            
            return $this;
        }
        
        public function addRecipient($recipientId)
        {
            if (!is_numeric($recipientId)) {
                throw new \InvalidArgumentException("Recipient ID must be numeric.");
            }
            $this->recipients[] = (int) $recipientId;
            
            return $this;
        }
        
        public function addRecpients($recipientIds)
        {
            if (!is_array($recipientIds) || $recipientIds instanceof \Traversable) {
                throw new \InvalidArgumentException("Recipient IDs must be traversable.");
            }
            try {
                foreach ($recipientIds as $recipientId) {
                    $this->setRecipient($recipientId);
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
    }