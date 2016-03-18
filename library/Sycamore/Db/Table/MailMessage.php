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

    namespace Sycamore\Table;
    
    use Sycamore\Db\Row\MailMessage;
    use Sycamore\Db\Table\AbstractObjectTable;
    
    /**
     * Table representation class for mail messages.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class MailMessage extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         */
        public function __construct(ServiceManager& $serviceManager)
        {
            parent::__construct($serviceManager, "mail_messages", new MailMessage());
        }
        
        /**
         * Gets mail messages by their sent status.
         * 
         * @param bool $sent The sent state to fetch against.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getBySent($sent, $forceDbFetch = false)
        {
            return $this->getByKey("sent", $sent, $forceDbFetch);
        }
        
        /**
         * Gets mail messages by their cancellation status.
         * 
         * @param bool $cancelled The cancellation state to fetch against.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByCancelled($cancelled, $forceDbFetch = false)
        {
            return $this->getByKey("cancelled", $cancelled, $forceDbFetch);
        }
        
        /**
         * Gets mail messages by their purpose.
         * 
         * @param string $purpose The purpose of the mail messages to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
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
         * @param int $sendTimeMin The minimum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
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
         * @param int $sendTimeMax The maximum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
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
         * @param int $sendTimeMin The minimum time that a mail message should be sent at.
         * @param int $sendTimeMax The maximum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return Zend\Db\ResultSet\ResultSet
         */
        public function getBySendTimeRange($sendTimeMin, $sendTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("sendTime", $sendTimeMin, $sendTimeMax, $forceDbFetch);
        }
    }
