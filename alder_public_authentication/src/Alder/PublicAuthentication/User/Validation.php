<?php
    
    namespace Alder\PublicAuthentication\User;
    
    use Alder\Container;
    use Alder\Error\Stack as ErrorStack;
    
    /**
     * Provides utility functions for validating user data.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Validation
    {
        /**
         * Validate that the given username is of correct format and
         * is not already in use.
         *
         * @param string             $username The username to validate.
         * @param \Alder\Error\Stack $errors   The error stack to push errors onto.
         *
         * @return bool True if valid, false otherwise.
         */
        public static function validateUsername($username, ErrorStack& $errors) {
            return (self::isUsername($username, $errors) && self::isUniqueUsername($username, $errors));
        }
        
        /**
         * Validate that the given email is of correct format and
         * is not already in use.
         *
         * @param string             $email  The email to validate.
         * @param \Alder\Error\Stack $errors The error stack to push errors onto.
         *
         * @return bool True if valid, false otherwise.
         */
        public static function validateEmail($email, ErrorStack& $errors) {
            return (self::isEmail($email, $errors) && self::isUniqueEmail($email, $errors));
        }
        
        /**
         * Ascertains the given password is sufficiently strong.
         *
         * @param string             $password The password to validate.
         * @param \Alder\Error\Stack $errors   The error stack to push errors onto.
         *
         * @return bool True if password is sufficiently strong, false otherwise.
         */
        public static function validatePassword($password, ErrorStack& $errors) {
            $passwordConfig = Container::get()->get("config")["alder"]["public_authentication"]["password"];
            
            if ($passwordConfig["max_length"] > 0 && $passwordConfig["min_length"] > $passwordConfig["max_length"]) {
                // Cheeky way to ensure a return of false if there is a failure, but also get 100% code coverage.
                return false
                       & trigger_error("Password configuration malformed: minimum password length less than maximum length. Maximum length assumed to be infinite.",
                                       E_USER_WARNING);
            }
            
            $result = true;
            
            // Check password length.
            if (strlen($password) < $passwordConfig["min_length"]) {
                $errors->push(103030101);
                $result = false;
            } else {
                if ($passwordConfig["max_length"] > 0 && strlen($password) > $passwordConfig["max_length"]) {
                    $errors->push(103030102);
                    $result = false;
                }
            }
            
            // Check password has a letter and number.
            if (!preg_match("#[0-9a-zA-Z]+#", $password)) {
                $errors->push(103030103);
                $result = false;
            }
            
            // Check password has a capital letter.
            if ($passwordConfig["strictness"] == PASSWORD_STRICTNESS_HIGH
                || $passwordConfig["strictness"] == PASSWORD_STRICTNESS_STRICT
            ) {
                if (!preg_match("#[A-Z]+#", $password)) {
                    $errors->push(103030104);
                    $result = false;
                }
            }
            
            // Check password has a symbol.
            if ($passwordConfig["strictness"] == PASSWORD_STRICTNESS_STRICT) {
                if (!preg_match("#\W+#", $password)) {
                    $errors->push(103030105);
                    $result = false;
                }
            }
            
            return $result;
        }
        
        /**
         * Validate the format of the given username.
         *
         * @param string             $username The username to validate the format of.
         * @param \Alder\Error\Stack $errors   The error stack to push errors onto.
         *
         * @return bool True if valid format, false otherwise.
         */
        public static function isUsername($username, ErrorStack& $errors) {
            $usernameConfig = Container::get()->get("config")["alder"]["public_authentication"]["username"];
            $usernameLength = strlen($username);
            
            $result = true;
            
            if ($usernameLength > $usernameConfig["max_length"]) {
                $errors->push(103030201);
                $result = false;
            }
            
            if ($usernameLength < $usernameConfig["min_length"]) {
                $errors->push(103030202);
                $result = false;
            }
            
            if (preg_match("#[^a-zA-Z0-9\_]+#", $username)) {
                $errors->push(103030203);
                $result = false;
            }
            
            return $result;
        }
        
        /**
         * Validate the format of the given email.
         *
         * @param string             $email  The email to validate the format of.
         * @param \Alder\Error\Stack $errors The error stack to push errors onto.
         *
         * @return bool True if valid format, false otherwise.
         */
        public static function isEmail($email, ErrorStack& $errors) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors->push(103030301);
                
                return true;
            }
            
            return false;
        }
        
        /**
         * Validate the uniqueness of the given username.
         *
         * @param string             $username The username to validate the uniqueness of.
         * @param \Alder\Error\Stack $errors   The error stack to push errors onto.
         *
         * @return bool True if unique, false otherwise.
         */
        public static function isUniqueUsername($username, ErrorStack& $errors) {
            if (Container::get()->get("AlderTableCache")->fetchTable("User")->isUsernameUnique($username)) {
                $errors->push(103030401);
                
                return true;
            }
            
            return false;
        }
        
        /**
         * Validate the uniqueness of the given email.
         *
         * @param string             $email  The email to validate the uniqueness of.
         * @param \Alder\Error\Stack $errors The error stack to push errors onto.
         *
         * @return bool True if unique, false otherwise.
         */
        public static function isUniqueEmail($email, ErrorStack& $errors) {
            if (Container::get()->get("AlderTableCache")->fetchTable("User")->isEmailUnique($email)) {
                $errors->push(103030501);
                
                return true;
            }
            
            return false;
        }
    }
