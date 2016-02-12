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

    namespace Sycamore\Table;
    
    use Sycamore\Row\MailMessage;
    use Sycamore\Table\ObjectTable;
    
    class MailMessage extends ObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         */
        public function __construct()
        {
            parent::__construct("mail_messages", new MailMessage());
        }
        
        /**
         * Gets mail message objects by their sent status.
         * 
         * @param bool $sent
         * @param bool $forceDbFetch
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getBySent($sent, $forceDbFetch = false)
        {
            return $this->getByKey("sent", $sent, $forceDbFetch);
        }
        
        /**
         * Gets mail message objects by their cancelled status.
         * 
         * @param bool $cancelled
         * @param bool $forceDbFetch
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByCancelled($cancelled, $forceDbFetch = false)
        {
            return $this->getByKey("cancelled", $cancelled, $forceDbFetch);
        }
        
        /**
         * Gets mail message objects by their purpose.
         * 
         * @param string $purpose
         * @param bool $forceDbFetch
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByPurpose($purpose, $forceDbFetch = false)
        {
            return $this->getByKey("purpose", $purpose, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent after a certain time.
         * 
         * @param int $sendTimeMin
         * @param bool $forceDbFetch
         * 
         * @return Zend\Db\ResultSet\ResultSet
         */
        public function getBySendTimeMin($sendTimeMin, $forceDbFetch = false)
        {
            return $this->getByKeyGreaterThanOrEqualTo("sendTime", $sendTimeMin, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent before a certain time.
         * 
         * @param int $sendTimeMax
         * @param bool $forceDbFetch
         * 
         * @return Zend\Db\ResultSet\ResultSet
         */
        public function getBySendTimeMax($sendTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyLessThanOrEqualTo("sendTime", $sendTimeMax, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent within a time range.
         * 
         * @param int $sendTimeMin
         * @param int $sendTimeMax
         * @param bool $forceDbFetch
         * 
         * @return Zend\Db\ResultSet\ResultSet
         */
        public function getBySendTimeRange($sendTimeMin, $sendTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("sendTime", $sendTimeMin, $sendTimeMax, $forceDbFetch);
        }
    }
    