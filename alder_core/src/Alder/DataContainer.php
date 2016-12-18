<?php
    
    namespace Alder;
    
    // TODO(Matthew): Not needed/suitable as is given "default" source should be installation-only data and "cache" source
    //                the installed-only data.
    /**
     * Provides functionality for containers that retrieve their data from a cache file first or a default file second.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class DataContainer
    {
        /**
         * The single instance of the container.
         *
         * @var self
         */
        protected static $instances = [];
        
        /**
         * Create a new container instance if none exists, return the container.
         *
         * @param string $defaultPath The default path to find the data at.
         * @param string $cachePath   The path to where the data is cached.
         * @param string $expectedObj The expected object type to be acquired from the cache.
         *
         * @return \Alder\DataContainer The container instance.
         */
        public static function create(string $defaultPath, string $cachePath,
                                      string $expectedObj = null) : DataContainer {
            $class = get_called_class();
            if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class($defaultPath, $cachePath, $expectedObj);
            }
            
            return self::$instances[$class];
        }
        
        /**
         * The filepath of the default data.
         *
         * @var string
         */
        protected $defaultPath = "";
        
        /**
         * The filepath of the cached data.
         *
         * @var string
         */
        protected $cachePath = "";
        
        /**
         * The container data.
         *
         * @var mixed;
         */
        protected $data = null;
        
        /**
         * Prepares the data, fetching from the filesystem if cached, constructing from default settings otherwise.
         *
         * @param string      $defaultPath The default path to find the data at.
         * @param string      $cachePath   The path to where the data is cached.
         * @param string|null $expectedObj The expected object type to be acquired from the cache.
         *
         * @throws \InvalidArgumentException If the provided default path is invalid.
         */
        protected function __construct(string $defaultPath, string $cachePath, ?string $expectedObj) {
            // Throw an exception if the default data source is non-existent.
            /**
             * @var \Zend\I18n\Translator\Translator $translator
             */
            $translator = DiContainer::get()->get("translator");
            if (!is_readable($defaultPath)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        $translator->translate("default_data_path_invalid", "core", $translator->getFallbackLocale()),
                        get_called_class()
                    )
                );
            }
            
            $this->defaultPath = $defaultPath;
            $this->cachePath = $cachePath;
            
            // Acquire the data from cache first (unserialising the contents), or from the default data source if no cache file exists.
            if (file_exists($cachePath)) {
                $data = unserialize(file_get_contents($cachePath));
                if ($expectedObj && !is_a($data, $expectedObj)) {
                    $this->acquireDefaultData();
                } else {
                    $this->data = $data;
                }
            } else {
                $this->acquireDefaultData();
            }
        }
        
        /**
         * Fetches the current data.
         *
         * @return mixed The current data.
         */
        public function get() {
            return $this->data;
        }
        
        /**
         * Saves the custom ACL object to cache.
         *
         * @return int|bool Number of bytes written on success, false on failure.
         */
        public function save() {
            return file_put_contents($this->cachePath, serialize($this->data));
        }
        
        /**
         * Acquires the default ACL and saves it to the cache.
         */
        protected function acquireDefaultData() : void {
            $this->data = require $this->defaultPath;
            $this->save();
        }
    }
