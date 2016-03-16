<?php

/* 
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
 */

    namespace Sycamore\Table;
    
    use Sycamore\Db\Row\NewsletterSubscriber;
    use Sycamore\Db\Table\AbstractObjectTable;
    
    /**
     * Table representation class for newsletter subscribers.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class NewsletterSubscriber extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         */
        public function __construct(ServiceManager& $serviceManager)
        {
            parent::__construct($serviceManager, "newsletter_subscribers", new NewsletterSubscriber());
        }
        
        /**
         * Gets a newsletter subscriber by their email.
         * 
         * @param string $email The email of the newsletter subscriber to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\NewsletterSubscriber
         */
        public function getByEmail($email, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("email", $email, $forceDbFetch);
        }
        
        /**
         * Gets the matching newsletter subscribers by their emails.
         * 
         * @param array $emails The emails of the newsletter subscribers to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet
         */
        public function getByEmails($emails, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("email", $emails, $forceDbFetch);
        }
        
        /**
         * Checks if the given email is unique.
         * 
         * @param string $email The email to check for uniqueness amongst newsletter subscribers.
         * 
         * @return boolean True if given email is unique, false otherwise.
         */
        public function isEmailUnique($email)
        {
            return !$this->select(array("email" => (string) $email))->current();
        }
    }