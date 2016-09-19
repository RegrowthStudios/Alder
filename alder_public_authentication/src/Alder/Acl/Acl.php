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
        const ACL_CACHE = CACHE_DIRECTORY . DIRECTORY_SEPARATOR . "acl/acl.cache";
        
        /**
         *
         * @var \Zend\Permissions\Acl\Acl;
         */
        protected $acl;
        
        public function __construct()
        {
            // Retrieve ACL from filesystem if it exists and is valid.
            if (file_exists(self::ACL_CACHE)) {
                $acl = unserialize(file_get_contents(self::ACL_CACHE));
                if (!$acl instanceof Acl) {
                    $this->acl = new Acl();
                } else {
                    $this->acl = $acl;
                }
            } else {
                $this->acl = new Acl();
            }
            
            
        }
    }
