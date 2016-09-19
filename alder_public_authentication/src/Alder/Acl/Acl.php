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
         * Cache file for the ACL object.
         */
        const ACL_CACHE = CACHE_DIRECTORY . DIRECTORY_SEPARATOR . "acl/acl.cache";
        
        /**
         *
         * @var \Zend\Permissions\Acl\Acl;
         */
        protected $acl = NULL;
        
        /**
         * The single instance of the ACL.
         *
         * @var \Alder\Acl\Acl
         */
        protected static $instance = NULL;
        
        /**
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
         * Prepares the ACL object, fetching from the filesystem if cached, constructing from default settings otherwise.
         */
        protected function __construct() {
            // TODO(Matthew): Measure performance metrics of unserialize vs reconstructing ACL each time.
            // Retrieve ACL from filesystem if it exists and is valid.
            if (file_exists(self::ACL_CACHE)) {
                $acl = unserialize(file_get_contents(self::ACL_CACHE));
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
        
        protected function acquireDefaultAcl()
        {
            
        }
    }
