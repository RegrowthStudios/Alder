<?php
    namespace Sycamore\Config;
    
    use Sycamore\Config\Exception\InvalidConfigException;
    use Sycamore\Stdlib\ArrayUtils;
    
    use Zend\Config\Config as ZendConfig;
    use Zend\Config\Writer\PhpArray;
    
    /**
     * Provides utility functions for editing config files.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     */
    class ConfigUtils
    {
        /**
         * Saves the given config object to the given filename.
         * 
         * @static
         * 
         * @param string $filename The filename at which to save the config to.
         * @param array|\Traversable|\Zend\Config\Config $config The configuration object to be saved.
         * 
         * @return bool True on successful save.
         * 
         * @throws \Sycamore\Config\Exception\InvalidConfigException If the config object wasn't in an expected form.
         * @throws \InvalidArgumentException If the filename is blank.
         * @throws \RuntimeException If there was an error writing to the file.
         */
        public static function save($filename, $config)
        {
            // Construct writer.
            $writer = new PhpArray();
            $writer->setUseBracketArraySyntax(true);
            
            // Get config into expected form.
            if ($config instanceof ZendConfig) {
                $config = $config->toArray();
            } else if ($config instanceof \Traversable) {
                $config = ArrayUtils::iteratorToArray($config);
            } else if (!is_array($config)) {
                throw new InvalidConfigException("The config was not in any of the expected forms; iterator, array or Zend Config object.");
            }
            
            // Save config to given file.
            try {
                $writer->toFile($filename, $config);
            } catch (\InvalidArgumentException $invArgEx) {
                throw $invArgEx;
            }
            
            return true;
        }
    }
