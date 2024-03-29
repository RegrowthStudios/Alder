<?php
    
    namespace Alder\Acl;
    
    use Alder\DataContainer;

    /**
     * Provides functionality for retrieving and storing ACL data as well as checking access rights of users against it.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class AclContainer extends DataContainer
    {
        /**
         * Filepath to configuration for the default ACL object.
         */
        protected const DEFAULT_ACL_FILEPATH = CONFIG_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR
                                               . "acl.default.php";
        
        /**
         * Filepath to cache for the custom ACL object.
         */
        protected const CUSTOM_ACL_FILEPATH = CACHE_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR
                                              . "acl.cache";
        
        public static function create() : DataContainer {
            return parent::create(self::DEFAULT_ACL_FILEPATH, self::CUSTOM_ACL_FILEPATH);
        }
    }
