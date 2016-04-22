<?php
    namespace Sycamore\User;
    
    use Sycamore\Error\Error;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Validation holds functions for checking the validity of user account details.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Validation
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
         * Ascertains the given username is both correctly formatted and unique.
         * 
         * @param string $username The username to validate.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if username is valid format and unique, false otherwise.
         */
        public function validateUsername($username, & $errors)
        {
            if ($this->isUsername($username, $errors) && $this->isUniqueUsername($username, $errors)) {
                return true;
            }
            return false;
        }
        
        /**
         * Ascertains the given email is both correctly formatted and unique.
         * 
         * @param string $email The username to validate.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if email is valid format and unique, false otherwise.
         */
        public function validateEmail($email, & $errors)
        {
            if ($this->isEmail($email, $errors) && $this->isUniqueEmail($email, $errors)) {
                return true;
            }
            return false;
        }
        
        /**
         * Ascertains the given password is sufficiently strong.
         * 
         * @param string $password The password to validate strength of.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if password is sufficiently strong, false otherwise.
         */
        public function passwordStrengthCheck($password, & $errors)
        {
            $passwordConfig = $this->serviceManager->get("Config")["Sycamore"]["security"]["password"];
            
            $result = true;
            
            if ($passwordConfig["maximumLength"] > 0 && $passwordConfig["minimumLength"] > $passwordConfig["maximumLength"]) {
                // Cheeky way to ensure a return of false if there is a failure, but also get 100% code coverage.
                return false & trigger_error("Password configuration malformed: minimum password length less than maximum length. Maximum length assumed to be infinite.", E_USER_WARNING);
            }
            
            // Check password length.
            if (strlen($password) < $passwordConfig["minimumLength"]) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_password_too_short");
            } else if ($passwordConfig["maximumLength"] > 0 && strlen($password) > $passwordConfig["maximumLength"]) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_password_too_long");
            }
            
            // Check password has a number.
            if (!preg_match("#[0-9]+#", $password)) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_password_missing_number");
            }
            
            // Check password has a letter, capital or otherwise.
            if (!preg_match("#[a-zA-Z]+#", $password)) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_password_missing_letter");
            }
            
            // Check password has a capital letter.
            if ($passwordConfig["strictness"] == PASSWORD_STRICTNESS_HIGH || $passwordConfig["strictness"] == PASSWORD_STRICTNESS_STRICT) {
                if (!preg_match("#[A-Z]+#", $password)) {
                    $result = false;
                    $errors[] = Error::create($this->serviceManager, "error_password_missing_capital_letter");
                }
            }
            
            // Check password has a symbol.
            if ($passwordConfig["strictness"] == PASSWORD_STRICTNESS_STRICT) {
                if (!preg_match("#\W+#", $password)) {
                    $result = false;
                    $errors[] = Error::create($this->serviceManager, "error_password_missing_symbol");
                }
            }
            
            return $result;
        }
        
        /**
         * Ascertains the given string is in email format.
         *
         * @param string $email The email to check format of.
         * @param array& $errors Errors generated are dumped here.
         *
         * @return bool True if email is valid format, false otherwise.
         */
        public function isEmail($email, & $errors)
        {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = Error::create($this->serviceManager, "invalid_email_format");
                return false;
            }
            return true;
        }
        
        /**
         * Ascertains the given email is not already registered in the database of users.
         * 
         * @param string $email The email whose uniqueness is to be checked.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if email is unique, false otherwise.
         */
        public function isUniqueEmail($email, & $errors)
        {
            $tableCache = $this->serviceManager->get("SycamoreTableCache");
            $userTable = $tableCache->fetchTable("User");
            if (!$userTable->isEmailUnique($email)) {
                $errors[] = Error::create($this->serviceManager, "none_unique_email");
                return false;
            }
            return true;
        }
        
        /**
         * Ascertains the given username is correctly formatted.
         * 
         * @param string $username The username to check format of.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if username is valid format, false otherwise.
         */
        public function isUsername($username, & $errors)
        {
            $usernameConfig = $this->serviceManager->get("Config")["Sycamore"]["username"];
            
            $result = true;
            
            // Check length of username.
            if (strlen($username) > $usernameConfig["maximumLength"]) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_username_too_long");
            } else if (strlen($username) < $usernameConfig["minimumLength"]) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_username_too_short");
            }
            
            // Check characters in username.
            if (preg_match("#[^a-zA-Z0-9\_]+#", $username)) {
                $result = false;
                $errors[] = Error::create($this->serviceManager, "error_username_invalid_character");
            }
            
            return $result;
        }
        
        /**
         * Ascertains if the given username is not already registered in the user database.
         * 
         * @param string $username The username whose uniqueness is to be checked.
         * @param array& $errors Errors generated are dumped here.
         * 
         * @return bool True if username is unique, false otherwise.
         */
        public function isUniqueUsername($username, & $errors)
        {
            $tableCache = $this->serviceManager->get("SycamoreTableCache");
            $userTable = $tableCache->fetchTable("User");
            if (!$userTable->isUsernameUnique($username)) {
                $errors[] = Error::create($this->serviceManager, "none_unique_username");
                return false;
            }
            return true;
        }
    }