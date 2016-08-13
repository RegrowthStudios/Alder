<?php
    
    namespace Alder\Db;
    
    class TableCache
    {
        /**
         * The namespace of the tables to be cached.
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
         * Prepares a table cache.
         * 
         * @param string $namespace The default namespace for the tables.
         */
        public function __construct($namespace) {
            $this->namespace = $namespace;
        }
        
        /**
         * Set the default namespace in which the tables reside.
         * 
         * @param string $namespace The default namespace to be set.
         */
        public function setDefaultNamespace($namespace) {
            $this->namespace = $namespace;
        }
        
        /**
         * Get the current default namespace.
         * 
         * @return string The default namespace currently set.
         */
        public function getDefaultNamespace() {
            return $this->namespace;
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
        public function fetchTable($tableName, $namespace = NULL)
        {
            if (!isset(self::$tables[$tableName])) {
                $classPath = ($namespace ? $namespace : self::$namespace) . $tableName;
                if (!class_exists($classPath)) {
                    throw new \InvalidArgumentException("No table exists by the name of $tableName.");
                }
                self::$tables[$tableName] = new $classPath();
            }
            
            return self::$tables[$tableName];
        }   
    }
