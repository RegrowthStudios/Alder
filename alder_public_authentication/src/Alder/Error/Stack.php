<?php
    
    namespace Alder\Error;
    
    use Alder\Error\Error;
    
    /**
     * Stores errors in order of addition.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Stack
    {
        // TODO(Matthew): Implement funcs for for looping.
        protected $errorCodes = [];
        
        public function push($code) {
            $this->errorCodes[] = $code;
        }
        
        public function pop() {
            return array_pop($this->errorCodes);
        }
        
        public function shift() {
            return array_shift($this->errorCodes);
        }
        
        public function retrieveErrors($clear = true) {
            $result = [];
            foreach ($this->errorCodes as $code) {
                $result[$code] = Error::retrieveString($code);
            }
            if ($clear) {
                $this->clearErrors();
            }
            return $result;
        }
        
        /**
         * Clears all errors stored in the container.
         */
        public function clearErrors() {
            $this->errors = [];
        }
    }
