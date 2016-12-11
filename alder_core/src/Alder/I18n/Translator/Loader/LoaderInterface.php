<?php
    
    namespace Alder\I18n\Translator\Loader;
    
    use Alder\I18n\Translator\Translator;
    
    use Zend\Cache\Storage\StorageInterface;
    
    interface LoaderInterface
    {
        /**
         * Set the cache object of the loader.
         *
         * @param \Zend\Cache\Storage\StorageInterface $cache The cache object to be used for caching purposes.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         */
        public function setCache(StorageInterface $cache) : LoaderInterface;
        
        /**
         * Sets the default base directory of the loader.
         *
         * @param string $directory The directory to set as the default base.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         */
        public function setDefaultBaseDirectory(string $directory) : LoaderInterface;
        
        /**
         * Adds a file to the loader for loading messages from.
         *
         * The files existence will be asserted before being added. If it does not match an existing file, a
         * FileNotFoundException will be thrown.
         *
         * @param string      $filename      The file path relative to the base directory.
         * @param string      $locale        The locale of the messages in the file.
         * @param string      $domain        The domain in which the files belong.
         * @param string|null $baseDirectory The base directory of the translation file.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         *
         * @throws \Alder\I18n\Translator\Loader\Exception\FileNotFoundException
         */
        public function addTranslationFile(string $filename, string $locale,
                                           string $domain = Translator::DEFAULT_DOMAIN,
                                           string $baseDirectory = null) : LoaderInterface;
        
        /**
         * Adds a file pattern to this loader for matching language files to.
         *
         * @param string      $pattern       The pattern that files desired to be loaded match.
         * @param string      $domain        The domain in which the files belong.
         * @param string|null $baseDirectory The base directory of the translation file.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         */
        public function addTranslationFilePattern(string $pattern, string $domain = Translator::DEFAULT_DOMAIN,
                                                  string $baseDirectory = null) : LoaderInterface;
        
        /**
         * Loads messages from currently set file patterns in the provided domain.
         *
         * @param string      $domain The domain in which to retrieve messages from.
         * @param string|null $locale The locale of messages to be retrieved.
         *
         * @return array
         */
        public function loadMessages(string $domain, string $locale = null) : ?array;
    }
