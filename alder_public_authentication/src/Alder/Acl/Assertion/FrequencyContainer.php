<?php
    
    namespace Alder\Acl\Assertion;
    
    use Alder\Stdlib\Container;
    
    /**
     * Provides a stack for passing multiple assertions into ACL rules.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class FrequencyContainer extends Container
    {
        /**
         * Filepath to configuration for the default Frequency array.
         */
        const DEFAULT_FREQ_FILEPATH = CONFIG_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR . "frequency.default.php";
    
        /**
         * Filepath to cache for the custom Frequency array.
         */
        const CUSTOM_FREQ_FILEPATH = CACHE_DIRECTORY . DIRECTORY_SEPARATOR . "acl" . DIRECTORY_SEPARATOR . "frequency.cache";
    
        public static function create() {
            return parent::create(self::DEFAULT_FREQ_FILEPATH, self::CUSTOM_FREQ_FILEPATH);
        }
    }
