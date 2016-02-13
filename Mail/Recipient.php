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

    use Sycamore\Mail\Message;
    use Sycamore\Utils\TableCache;

    class Recipient
    {

        /**
         * Stores the recipient's ID in their appropriate table.
         *
         * @var int
         */
        protected $recipientId;

        /**
         * Stores the recipients type.
         *
         * @var string
         */
        protected $recipientType;

        public function __construct($recipientId, $recipientType)
        {
            if (!is_numeric($recipientId)) {
                throw new \InvalidArgumentException("ID of recipient must be numeric.");
            }
            if (!is_string($recipientType)) {
                throw new \InvalidArgumentException("Recipient type must be a string.");
            } else if (!in_array(strtoupper($recipientType), array_keys(Message::TYPES))) {
                throw new \InvalidArgumentException("Recipient type must be one of those defined in Recipient::recipientTypes.");
            }
            $this->recipientId = (int) $recipientId;
            $this->recipientType = strtoupper($recipientType);
        }

        /**
         * Gets most up to date recipient data and returns in array form.
         * Returns false if the recipient for which the instance was created doesn't exist.
         *
         * @return array|boolean
         */
        public function getRecipientData()
        {
            // Fetch set recipient type's config. (I promise this works if the IDE complains, feckin' things need to get with bitchin' 5.6).
            $recipientTypeData = self::RECIPIENT_TYPES[$this->recipientType];

            // Fetch table for recipient.
            $table = TableCache::getTableFromCache($recipientTypeData["table"]);

            // Fetch recipient's DB entry.
            $recipientRaw = $table->getById($this->recipientId);

            // Check recipient exists.
            if (!$recipientRaw) {
                return false;
            }

            // Construct array of required recipient data.
            $recipient = array();
            foreach ($recipientTypeData["keys"] as $key) {
                $recipient[$key] = $recipientRaw->$key;
            }

            // Pass data array back.
            return $recipient;
        }
    }
