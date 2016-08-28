<?php
    
    namespace Alder\Error;
    
    use Alder\Error\Stack;
    
    /**
     * Stores a stack of error stacks for, e.g., multiple action requests.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Container
    {
        // TODO(Matthew): Implement funcs for for looping.{
        // TODO(Matthew): Implement array access.
        protected $stacks = [];
        
        public function create() {
            $this->stacks[] = new Stack();
        }
        
        public function push(Stack $stack) {
            $this->stacks[] = $stack;
        }
        
        public function pop() {
            return array_pop($this->stacks);
        }
        
        public function shift() {
            return array_shift($this->stacks);
        }
    }
