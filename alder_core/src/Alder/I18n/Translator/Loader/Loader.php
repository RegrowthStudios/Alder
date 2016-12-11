<?php
    
    namespace Alder\I18n\Translator\Loader;
    
    use Alder\I18n\Translator\Loader\Exception\FileNotFoundException;
    use Alder\I18n\Translator\Translator;
    
    use Zend\Cache\Storage\StorageInterface;
    
    class Loader implements LoaderInterface
    {
        /**
         * @var \Zend\Cache\Storage\StorageInterface $cache
         */
        protected $cache;
        
        /**
         * @var string
         */
        protected $defaultBaseDirectory;
        
        /**
         * @var array
         */
        protected $messages = [];
        
        /**
         * @var array
         */
        protected $files = [];
        
        /**
         * @var array
         */
        protected $filePatterns = [];
        
        /**
         * {@inheritdoc}
         */
        public function setCache(StorageInterface $cache) : LoaderInterface {
            $this->cache = $cache;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function setDefaultBaseDirectory(string $directory) : LoaderInterface {
            $this->defaultBaseDirectory = $directory;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function addTranslationFile(string $filename, string $locale,
                                           string $domain = Translator::DEFAULT_DOMAIN,
                                           string $baseDirectory = null) : LoaderInterface {
            $filepath = ($baseDirectory ?: $this->defaultBaseDirectory) . $filename;
            if (!is_file($filepath)) {
                throw new FileNotFoundException("No language file with path: '" . $filepath . "' could be found.");
            }
            if (!isset($this->files[$domain])) {
                $this->files[$domain] = [];
            }
            if (!isset($this->files[$domain][$locale])) {
                $this->files[$domain][$locale] = [];
            }
            $this->files[$domain][$locale][] = $filepath;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function addTranslationFilePattern(string $pattern, string $domain = Translator::DEFAULT_DOMAIN,
                                                  string $baseDirectory = null) : LoaderInterface {
            if (!isset($this->filePatterns[$domain])) {
                $this->filePatterns[$domain] = [];
            }
            $this->filePatterns[$domain] = ($baseDirectory ?: $this->defaultBaseDirectory) . $pattern;
            
            return $this;
        }
        
        // TODO(Matthew): Allow alternative storage methods than PHP arrays?
        // TODO(Matthew): Only grabbing files if no locale is specified might be slightly odd.
        //                Require a locale be specified? List all allowed locales for a file
        //                pattern?
        /**
         * {@inheritdoc}
         */
        public function loadMessages(string $domain, string $locale = null) : ?array {
            if (!isset($this->filePatterns[$domain])
                && (!isset($this->files[$domain])
                    || ($locale ? !isset($this->files[$domain][$locale]) : empty($this->files[$domain])))
            ) {
                return null;
            }
            
            $messages = [];
            if ($locale) {
                foreach ($this->filePatterns[$domain] as $filePattern) {
                    foreach (glob(sprintf($filePattern, $locale)) as $file) {
                        $messages = array_merge($messages, include $file);
                    }
                }
                foreach ($this->files[$domain][$locale] as $file) {
                    $messages = array_merge($messages, include $file);
                }
            } else {
                foreach ($this->files[$domain] as $_locale => $files) {
                    foreach ($files as $file) {
                        $messages[$_locale] = array_merge($messages[$_locale], include $file);
                    }
                }
            }
            
            return $messages;
        }
        
        /**
         * Initialises member variables.
         *
         * @param string|null $defaultBaseDirectory
         * @param \Zend\Cache\Storage\StorageInterface|null $cache
         */
        public function __construct(string $defaultBaseDirectory = null, StorageInterface $cache = null) {
            $this->defaultBaseDirectory = $defaultBaseDirectory;
            $this->cache = $cache;
        }
    }
