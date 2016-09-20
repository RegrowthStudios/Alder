<?php

    namespace Alder\Acl;
    
    use Zend\Permissions\Acl\Acl;

    /**
     * Provides functionality for retrieving and storing ACL data as well as checking access rights of users against it.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Acl
    {
        /**
         * Filepath to configuration for the default ACL object.
         */
        const DEFAULT_ACL_FILEPATH = CONFIG_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR . "acl.default.php";
        
        /**
         * Filepath to cache for the custom ACL object.
         */
        const CUSTOM_ACL_FILEPATH = CACHE_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR . "acl.cache";
        
        /**
         * The single instance of the ACL.
         *
         * @var \Alder\Acl\Acl
         */
        protected static $instance = NULL;
        
        /**
         * Create an ACL instance, or get the existing one.
         * 
         * @return \Alder\Acl\Acl The created instance of the Acl.
         */
        public static function create()
        {
            if (!isset(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        /**
         * The actual ACL object.
         *
         * @var \Zend\Permissions\Acl\Acl;
         */
        protected $acl = NULL;
        
        /**
         * Prepares the ACL object, fetching from the filesystem if cached, constructing from default settings otherwise.
         */
        protected function __construct() {
            // TODO(Matthew): Measure performance metrics of unserialize vs reconstructing ACL each time.
            // Retrieve ACL from filesystem if it exists and is valid.
            if (file_exists(self::CUSTOM_ACL_FILEPATH)) {
                $acl = unserialize(file_get_contents(self::CUSTOM_ACL_FILEPATH));
                if (!$acl instanceof Acl) {
                    $this->acquireDefaultAcl();
                } else {
                    $this->acl = $acl;
                }
            } else {
                $this->acquireDefaultAcl();
            }
        }
        
        /**
         * Fetches the current ACL object.
         * 
         * @return \Zend\Permissions\Acl\Acl The current ACL object.
         */
        public function get()
        {
            return $this->acl;
        }
        
        /**
         * Saves the custom ACL object to cache.
         * 
         * @return int|bool Number of bytes written on success, false on failure.
         */
        public function save()
        {
            return file_put_contents(self::CUSTOM_ACL_FILEPATH, serialize($this->acl));
        }
                
        /**
         * Acquires the default ACL and saves it to the cache.
         */
        protected function acquireDefaultAcl()
        {
            $this->acl = require self::DEFAULT_ACL_FILEPATH;
            $this->save();
        }
    }
