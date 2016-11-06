<?php
    
    namespace Alder\Stdlib;
    
    /**
     * Provides functionality for containers that retrieve their data from a cache first or a default object second.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Container
    {
        /**
         * The single instance of the container.
         *
         * @var self
         */
        protected static $instance = null;
        
        /**
         * Create a new container instance if none exists, return the container.
         *
         * @return self The container instance.
         */
        public static function create(string $defaultPath, string $cachePath) {
            if (!isset(self::$instance)) {
                self::$instance = new self($defaultPath, $cachePath);
            }
            
            return self::$instance;
        }
        
        /**
         * The container data.
         *
         * @var mixed;
         */
        protected $data = null;
        
        /**
         * The filepath of the default data.
         *
         * @var string
         */
        protected $defaultPath = "";
        
        /**
         * The filepath of the cached data.
         *
         * @var string
         */
        protected $cachePath = "";
        
        /**
         * Prepares the data, fetching from the filesystem if cached, constructing from default settings otherwise.
         */
        protected function __construct(string $defaultPath, string $cachePath, string $expectedObj = null) {
            $this->defaultPath = $defaultPath;
            $this->cachePath = $cachePath;
            
            if (file_exists($cachePath)) {
                $data = unserialize(file_get_contents($cachePath));
                if ($expectedObj && !is_a($data, $expectedObj)) {
                    $this->acquireDefaultData();
                } else {
                    $this->container = $data;
                }
            } else {
                $this->acquireDefaultData();
            }
        }
        
        /**
         * Fetches the current data.
         *
         * @return mixed The current data.
         */
        public function get() {
            return $this->data;
        }
        
        /**
         * Saves the custom ACL object to cache.
         *
         * @return int|bool Number of bytes written on success, false on failure.
         */
        public function save() {
            return file_put_contents($this->cachePath, serialize($this->container));
        }
        
        /**
         * Acquires the default ACL and saves it to the cache.
         */
        protected function acquireDefaultData() : void {
            $this->container = require $this->defaultPath;
            $this->save();
        }
    }
