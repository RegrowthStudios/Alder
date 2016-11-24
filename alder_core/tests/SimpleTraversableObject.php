<?php
    namespace AlderTest;
    /**
     * Simple traversable object for testing with.
     */
    class SimpleTraversableObject implements \Iterator
    {
        protected $position = 0;
        protected $contents = [
            "test",
            "hello",
            "world",
        ];
        
        public function __construct(array $contents = NULL)
        {
            if ($contents !== NULL) {
                $this->contents = $contents;
            }
            $this->position = 0;
        }
        public function rewind()
        {
            $this->position = 0;
        }
        public function current()
        {
            return $this->contents[$this->position];
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
            return isset($this->contents[$this->position]);
        }
        
        public function &toArray()
        {
            return $this->contents;
        }
        
        public function __toString()
        {
            return implode($this->contents);
        }
    }
