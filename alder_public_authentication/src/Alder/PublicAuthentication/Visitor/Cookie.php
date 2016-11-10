<?php
    
    namespace Alder\PublicAuthentication\Visitor;
    
    use Alder\PublicAuthentication\Visitor\CookieInterface;
    
    /**
     * Provides functionality for getting and updating visitor session information both client-side and server-side.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Cookie implements CookieInterface
    {
        protected $data;
    
        /* Array Access Functions */
    
        public function offsetExists($offset) {
            return isset($this->data[$offset]);
        }
    
        public function offsetGet($offset) {
            if ($this->offsetExists($offset)) {
                return $this->data[$offset];
            }
            throw new \InvalidArgumentException("No data exists for the key at '" . $offset . "''.");
        }
    
        public function offsetSet($offset, $value) {
            $this->data[$offset] = $value;
        }
    
        public function offsetUnset($offset) {
            if ($this->offsetExists($offset)) {
                unset($this->data[$offset]);
            }
        }
    }
