<?php
    namespace Sycamore\Error;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    // TODO(Matthew): Move this elsewhere, i.e. into a language utility.
    /**
     * Provides utility function for creating error strings.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Error
    {
        /**
         * Constructs an error from its key.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         * @param string $key The key of the error to construct.
         * 
         * @return string The resulting error message.
         */
        public static function create(ServiceLocatorInterface& $serviceManager, $key)
        {
            // Grab raw error message.
            $errorMessage = $serviceManager->get("Language")->fetchPhrase($key);
            
            // Grab parameter paths embedded in message.
            $params = preg_split("/{((?:[a-zA-Z]+[\\\/])+[a-zA-Z]+)}/g", $errorMessage);            
            foreach ($params as $param) {
                // Split parameter path up into parts.
                $path = preg_split("/([a-zA-Z]+)/g", $param);
                
                // Set starting value from path and then iterate into final value.
                $value = $serviceManager->get("Config")["Sycamore"];
                foreach ($path as $pathPart) {
                    $value = $value[$pathPart];
                }
                
                // Replace param path with the value.
                $errorMessage = str_replace("{" . $param . "}", strval($value), $errorMessage);
            }
            
            // Return the formatted error message.
            return $errorMessage;
        }
    }
