<?php
    
    namespace Alder\I18n\Translator\Loader;
    
    use Alder\I18n\Translator\Translator;
    
    interface LoaderInterface
    {
        /**
         * Sets the default base directory of the loader.
         *
         * @param string $directory The directory to set as the default base.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         */
        public function setDefaultBaseDirectory(string $directory) : LoaderInterface;
        
        /**
         * Adds a file pattern to this loader for loading files from. Pattern, domain and
         * base directory are concatenated to form the final path pattern for the files to be
         * loaded.
         *
         * @param string $pattern       The pattern that files desired to be loaded match.
         * @param string $domain        The domain in which the files belong.
         * @param string $baseDirectory The base directory of the translation file.
         *
         * @return \Alder\I18n\Translator\Loader\LoaderInterface
         */
        public function addTranslationFilePattern(string $pattern, string $domain = Translator::DEFAULT_DOMAIN, string $baseDirectory = null) : LoaderInterface;
    
        /**
         * Loads messages from currently set translation patterns.
         *
         * @param string      $domain
         * @param string|null $locale
         */
        public function loadMessages(string $domain, string $locale = null) : void;
    }
