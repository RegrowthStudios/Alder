<?php
    namespace Sycamore\Cache;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Facilitates the caching of tables for more performant table acquisition.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     */
    class TableCache
    {
        /**
         * The namespace of the tables cached.
         *
         * @var string
         */
        protected $namespace;
        
        /**
         * The tables cached.
         *
         * @var array
         */
        protected $tables = [];
        
        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;
        
        /**
         * Prepares the table cache with the provided service manager and table namespace.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager of this application instance.
         * @param string $namespace The namespace of the tables to be cached.
         * 
         * @throws \InvalidArgumentException If $namespace is not a string.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager, $namespace)
        {
            if (!is_string($namespace)) {
                throw new \InvalidArgumentException("The namespace provided must be a string!");
            }
            $this->serviceManager = $serviceManager;
            $this->namespace = $namespace;
        }
        
        /**
         * Attempts to fetch the table with the given table name, returning it on success.
         * 
         * @param string $tableName The name of the table to be fetched.
         * 
         * @return \Sycamore\Db\Table\AbstractTable The fetched table.
         * 
         * @throws \InvalidArgumentException If no table exists with the given name.
         */
        public function fetchTable($tableName)
        {
            if (!isset($this->tables[(string) $tableName])) {
                $classPath = $this->namespace . $tableName;
                if (!class_exists($classPath)) {
                    throw new \InvalidArgumentException("No table exists by the name of $tableName.");
                }
                $this->tables[$tableName] = new $classPath($this->serviceManager);
            }
            
            return $this->tables[$tableName];
        }            
    }
