<?php
    namespace SycamoreTest;

    /**
     * Simple traversable object for testing with.
     */
    class SimpleTraversableObject implements \Iterator
    {
        protected $position = 0;
        protected $array = [
            "test",
            "hello",
            "world",
        ];
        
        public function __construct()
        {
            $this->position = 0;
        }

        public function rewind()
        {
            $this->position = 0;
        }

        public function current()
        {
            return $this->array[$this->position];
        }

        public function key()
        {
            return $this->position;
        }

        public function next()
        {
            ++$this->position;
        }

        public function valid()
        {
            return isset($this->array[$this->position]);
        }
        
        public function &toArray()
        {
            return $this->array;
        }
        
        public function __toString()
        {
            return implode($this->array);
        }
    }
