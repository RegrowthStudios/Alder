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
    class Stack implements \Iterator
    {
        /**
         * @var array The stack of error codes.
         */
        protected $errorCodes = [];

        /**
         * Pushes the provided error code onto the stack.
         *
         * @param int|string $code The error code to be added.
         */
        public function push($code) {
            $this->errorCodes[] = $code;
        }

        /**
         * Pops off the last error code of the stack and returns it.
         *
         * @return int|NULL The error code popped off, or NULL if the array is empty.
         */
        public function pop() {
            return array_pop($this->errorCodes);
        }

        /**
         * Shifts off the first error code of the stack and returns it.
         *
         * @return int|NULL The error code shifted off, or NULL if the array is empty.
         */
        public function shift() {
            return array_shift($this->errorCodes);
        }

        /**
         * Retrieves the error codes on the stack and their associated messages.
         *
         * @param bool $clear Whether to clear the stack afterwords.
         * @return array The error messages keyed by their respective error codes.
         */
        public function retrieve($clear = true) {
            $result = [];
            foreach ($this->errorCodes as $code) {
                $result[$code] = Error::retrieveString($code);
            }
            if ($clear) {
                $this->clear();
            }
            return $result;
        }

        /**
         * Determines if error codes exist on the stack.
         *
         * @return bool True if error codes on stack, false otherwise.
         */
        public function notEmpty() {
            return !empty($this->errorCodes);
        }
        
        /**
         * Clears all errors stored in the container.
         */
        public function clear() {
            $this->errorCodes = [];
        }

        /* Iterator functions. */

        protected $position = 0;

        public function rewind() {
            $this->position = 0;
        }

        public function current() {
            return $this->errorCodes[$this->position];
        }

        public function key() {
            return $this->position;
        }

        public function next() {
            ++$this->position;
        }

        public function valid() {
            return isset($this->errorCodes[$this->position]);
        }
    }
