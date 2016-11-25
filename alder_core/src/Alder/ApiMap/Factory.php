<?php
    
    namespace Alder\ApiMap;

    use Zend\Expressive\ConfigManager\ConfigManager;
    use Zend\Expressive\ConfigManager\PhpFileProvider;
    
    /**
     * The API map factory.
     *
     * Constructs an API map from all provided API map specifications.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Factory
    {
        /**
         * Constructs and returns the API map.
         */
        public static function create() : array {
            $apiMap = new ConfigManager([
                new PhpFileProvider(file_build_path(API_MAP_DIRECTORY, "*.php"))
            ], file_build_path(CACHE_DIRECTORY, "api_map", "api_map.php"));
            
            return $apiMap->getMergedConfig();
        }
    }
