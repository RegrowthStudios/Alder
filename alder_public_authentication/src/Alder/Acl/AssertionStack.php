<?php
    
    namespace Alder\Acl;
    
    use Zend\Permissions\Acl\Acl;
    use Zend\Permissions\Acl\Assertion\AssertionInterface;
    use Zend\Permissions\Acl\Resource\ResourceInterface;
    use Zend\Permissions\Acl\Role\RoleInterface;
    
    /**
     * Provides a stack for passing multiple assertions into ACL rules.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class AssertionStack implements AssertionInterface, \ArrayAccess, \Iterator
    {
        /**
         * The assertion stack.
         *
         * @var \Zend\Permissions\Acl\Assertion\AssertionInterface[]
         */
        protected $stack = [];
        
        /**
         * The current index of the iterator.
         *
         * @var int
         */
        protected $currIndex = 0;
        
        /**
         * Pushes a new assertion onto the stack.
         *
         * @param \Zend\Permissions\Acl\Assertion\AssertionInterface $assert The assertion to push to the end of the
         *                                                                   stack.
         */
        public function push(AssertionInterface $assert) : void {
            $stack[] = $assert;
        }
        
        /**
         * Pops an assertion off the end of a stack.
         *
         * @return \Zend\Permissions\Acl\Assertion\AssertionInterface $assert The assertion popped off the end of the
         *                                                            stack.
         */
        public function pop() : AssertionInterface {
            return array_pop($this->stack);
        }
        
        /**
         * Inserts a new assertion at the front of the stack.
         *
         * @param \Zend\Permissions\Acl\Assertion\AssertionInterface $assert The assertion to add to the front of the
         *                                                                   stack.
         */
        public function unshift(AssertionInterface $assert) : void {
            array_unshift($this->stack, $assert);
        }
        
        /**
         * Pops an assertion off the end of a stack.
         *
         * @return \Zend\Permissions\Acl\Assertion\AssertionInterface $assert The assertion shifted off the front of
         *                                                            the stack.
         */
        public function shift() : AssertionInterface {
            return array_shift($this->stack);
        }
        
        /**
         * @param int $offset
         *
         * @return \Zend\Permissions\Acl\Assertion\AssertionInterface|NULL
         */
        public function offsetGet($offset) : ?AssertionInterface {
            return $this->stack[$offset] ?? null;
        }
        
        /**
         * @param int $offset
         *
         * @return bool
         */
        public function offsetExists($offset) : bool {
            return isset($this->stack[$offset]);
        }
        
        /**
         * @param int                $offset
         * @param AssertionInterface $value
         */
        public function offsetSet($offset, $value) : void {
            $this->stack[$offset] = $value;
        }
        
        /**
         * @param int $offset
         */
        public function offsetUnset($offset) : void {
            if (isset($this->stack[$offset])) {
                unset($this->stack[$offset]);
            }
        }
        
        /**
         * @return \Zend\Permissions\Acl\Assertion\AssertionInterface
         */
        public function current() : AssertionInterface {
            return $this->stack[$this->currIndex];
        }
        
        /**
         * @return int
         */
        public function key() : integer {
            return $this->currIndex;
        }
        
        /**
         * @return void
         */
        public function next() : void {
            $this->currIndex++;
        }
        
        /**
         * @return void
         */
        public function rewind() : void {
            $this->currIndex = 0;
        }
        
        /**
         * @return bool
         */
        public function valid() : bool {
            return $this->offsetExists($this->stack[$this->currIndex]);
        }
        
        /**
         * {@inheritdoc}
         */
        public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null,
                               $privilege = null) : bool {
            foreach ($this->stack as $assert) {
                if (!$assert->assert($acl, $role, $resource, $privilege)) {
                    return false;
                }
            }
            
            return true;
        }
    }
