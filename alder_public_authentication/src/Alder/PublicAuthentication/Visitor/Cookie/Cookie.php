<?php
    
    namespace Alder\PublicAuthentication\Visitor\Cookie;
    
    use Alder\PublicAuthentication\Visitor\Cookie\CookieInterface;
    
    /**
     * Provides a basic wrapper for cookie data fetched. Extend this and add procedures then use the new class as a
     * prototype when fetching the related cookie.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Cookie implements CookieInterface
    {
        /**
         * Flag of whether this cookie has been initialised or not.
         *
         * @var bool
         */
        protected $initialised = false;
        
        /**
         * Stores the original data of the cookie.
         *
         * @var array
         */
        protected $originalData;
        
        /**
         * Stores the current data of the cookie.
         *
         * @var array
         */
        protected $data;
        
        /**
         * {@inheritdoc}
         */
        public function initialise(array $data) : ?CookieInterface {
            if ($this->initialised) {
                return null;
            }
            
            $this->data = $data;
            $this->originalData = $data;
            $this->initialised = true;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasChanged() : bool {
            return (bool) array_diff($this->originalData, $this->data);
        }
        
        /* Array Access Functions */
        
        /**
         * {@inheritdoc}
         */
        public function offsetExists($offset) {
            return isset($this->data[$offset]);
        }
        
        /**
         * {@inheritdoc}
         */
        public function offsetGet($offset) {
            if ($this->offsetExists($offset)) {
                return $this->data[$offset];
            }
            throw new \InvalidArgumentException("No data exists for the key at '" . $offset . "'.");
        }
        
        /**
         * {@inheritdoc}
         */
        public function offsetSet($offset, $value) {
            $this->data[$offset] = $value;
        }
        
        /**
         * {@inheritdoc}
         */
        public function offsetUnset($offset) {
            if ($this->offsetExists($offset)) {
                unset($this->data[$offset]);
            }
        }
    }
