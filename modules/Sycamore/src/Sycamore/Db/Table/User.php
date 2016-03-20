<?php
    namespace Sycamore\Db\Table;
    
    use Sycamore\Db\Row\User as UserRow;
    use Sycamore\Db\Table\AbstractObjectTable;

    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Table representation class for users.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class User extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager of this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            parent::__construct($serviceManager, "users", new UserRow());
        }
        
        /**
         * Gets a user by their username.
         * 
         * @param string $username The username of the user to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\User The fetched user.
         */
        public function getByUsername($username, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("username", $username, $forceDbFetch);
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
         * @param string $email The email of the user to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\User The fetched user.
         */
        public function getByEmail($email, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("email", $email, $forceDbFetch);
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