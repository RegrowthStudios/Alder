<?php
    namespace Sycamore\User;
    
    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Security holds functions for ensuring the security of the user experience.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Security
    {
        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;
        
        /**
         * Prepares the sercurity utility by injecting the service manager.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this instance of the application.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            $this->serviceManager = $serviceManager;
        }
        
        /**
         * Return hashed password.
         *
         * @var string $password The password to hash.
         *
         * @return string The hashed result.
         */
        public function hashPassword($password)
        {
            return password_hash($password, PASSWORD_DEFAULT, ["cost" => $this->serviceManager->get("Config")["Sycamore"]["security"]["passwordHashingStrength"] ]);
        }
        
        /**
         * Verifies given password is the same as given hash.
         *
         * @var string $password The password to verifiy.
         * @var string $hash The hash to verify against.
         *
         * @return bool True if the password is valid, false otherwise.
         */
        public function verifyPassword($password, $hash)
        {
            return password_verify($password, $hash);
        }
        
        /**
         * Verifies if the given password needs rehashing.
         *
         * @var string $password The password to determine the need for a rehash of.
         *
         * @return bool True if the password needs rehashing, false otherwise.
         */
        public function passwordNeedsRehash($password)
        {
            return password_needs_rehash($password, PASSWORD_DEFAULT, [ 'cost' => $this->serviceManager->get("Config")["Sycamore"]["security"]["passwordHashingStrength"] ]);
        }
    }