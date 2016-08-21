<?php

    namespace Alder\Db\Table;

    use Alder\Db\Row\User as UserRow;
    use Alder\Db\Table\AbstractTable;

    /**
     * Gateway for the user table.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class User extends AbstractTable
    {
        /**
         * Prepare the user table gateway, establishing the row prototype.
         */
        public function __construct()
        {
            parent::__construct("users", new UserRow());
        }

        /**
         * Gets a user by their ID.
         *
         * @param int $id The ID of the user to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Alder\Db\Row\User The fetched user.
         */
        public function getById($id, $forceDbFetch = false) {
            return $this->getUniqueByKey("id", $id, $forceDbFetch);
        }

        /**
         * Gets a user by their username.
         *
         * @param string $username The username of the user to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Alder\Db\Row\User The fetched user.
         */
        public function getByUsername($username, $forceDbFetch = false) {
            return $this->getUniqueByKey("username", $username, $forceDbFetch);
        }

        /**
         * Gets a collection of users by their usernames.
         *
         * @param array $usernames The usernames of the users to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched users.
         */
        public function getByUsernames($usernames, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("username", $usernames, $forceDbFetch);
        }

        /**
         * Gets a user by their email.
         *
         * @param string $emailLocal The local component of the email of the user to fetch.
         * @param string $emailDomain The domain component of the email of the user to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Alder\Db\Row\User The fetched user.
         */
        public function getByEmail($emailLocal, $emailDomain, $forceDbFetch = false)
        {
            return $this->getUniqueByMultipleKeys(["primary_email_local", "primary_email_domain"], [$emailLocal, $emailDomain], $forceDbFetch);
        }

        /**
         * Gets a collection of users by their emails.
         *
         * @param array $emails The emails of the users to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         *
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched users.
         */
        public function getByEmails($emails, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("email", $emails, $forceDbFetch);
        }

        /**
         * Checks if the given username is unique.
         *
         * @param string $username The username to check for uniqueness of amongst users.
         *
         * @return boolean True if given username is unique, false otherwise.
         */
        public function isUsernameUnique($username)
        {
            return !$this->select(["username" => (string) $username])->current();
        }

        /**
         * Checks if the given email is unique.
         *
         * @param string $email The email to check for uniqueness of amongst users.
         *
         * @return boolean True if given email is unique, false otherwise.
         */
        public function isEmailUnique($email)
        {
            return !$this->select(["email" => (string) $email])->current();
        }
    }
