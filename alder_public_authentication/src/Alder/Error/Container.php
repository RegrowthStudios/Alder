<?php
    
    namespace Alder\Error;
    
    use Alder\Container as DiContainer;
    
    /**
     * Container for error codes.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Container
    {
        /**
         * @var \Alder\Error\Container The instance of this class.
         */
        protected static $instance = NULL;

        /**
         * Sets up the single instance of this class if it doesn't exist, then returns the instance.
         *
         * @return \Alder\Error\Container The instance of this class.
         */
        public static function getInstance() {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * @var array The language data for the application.
         */
        protected $language;
        
        /**
         * @var array The errors held in this container.
         */
        protected $errors = [];
        
        /**
         * Empty constructor.
         */
        protected function __construct() {
            $this->language = DiContainer::get()->get("AlderLanguageData");
        }
        
        /**
         * Add an error to the stack of errors.
         * 
         * @param int|string $code The error code to add.
         * 
         * @throws \InvalidArgumentException Thrown if the error code provided is not numeric.
         */
        public function addError($code) {
            if (!is_numeric($code)) {
                throw new \InvalidArgumentException("Invalid error code provided.");
            }
            $this->errors[] = (int) $code;
        }
        
        /**
         * Retrieve an error string corresponding to the provided error code.
         * 
         * @param int|string $code The error code to add.
         *
         * @return string The corresponding error string.
         *
         * @throws \InvalidArgumentException Thrown if the error code provided is not numeric.
         */
        public function retrieveErrorString($code) {
            if (!is_numeric($code)) {
                throw new \InvalidArgumentException("Invalid error code provided.");
            }
            return $this->language[$code];
        }
        
        /**
         * Retrieve errors and corresponding strings.
         *
         * @param bool $clear Whether to clear out errors or not.
         *
         * @return array The error strings and added errors.
         */
        public function retrieveErrors($clear = true) {
            $result = [];
            foreach ($this->errors as $error) {
                $result[$error] = $this->language[$error];
            }
            if ($clear) $this->clearErrors();
            return $result;
        }

        /**
         * Determines if any errors are stored in the container.
         *
         * @return bool True if errors are stored, false otherwise.
         */
        public function hasErrors() {
            return !empty($this->errors);
        }

        /**
         * Clears all errors stored in the container.
         */
        public function clearErrors() {
            $this->errors = [];
        }
    }
