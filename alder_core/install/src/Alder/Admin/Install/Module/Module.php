<?php
    
    namespace Alder\Admin\Install\Module;
    
    use Alder\Admin\Install\Module\Exception\MalformedInfoException;
    
    use MikeRoetgers\DependencyGraph\Operation;
    
    class Module implements Operation
    {
        /**
         * @var string
         */
        protected $name                    = "";

        /**
         * @var string
         */
        protected $description             = "";

        /**
         * @var string
         */
        protected $author                  = "";
        
        /**
         * @var string
         */
        protected $currentVersion          = "";
        
        /**
         * @var array
         */
        protected $currentSoftDependencies = [];
        
        /**
         * @var array
         */
        protected $currentHardDependencies = [];
        
        /**
         * @var string
         */
        protected $futureVersion           = "";
        
        /**
         * @var array
         */
        protected $futureSoftDependencies  = [];
        
        /**
         * @var array
         */
        protected $futureHardDependencies  = [];
        
        /**
         * @var string[]
         */
        protected $tags = [];
        
        /**
         * Info constructor.
         *
         * @param string $module
         */
        public function __construct(string $module) {
            $this->tags[] = $module;
            
            list( $_,
                  $_,
                  $_,
                  $this->currentVersion,
                  $this->currentSoftDependencies,
                  $this->currentHardDependencies ) = $this->getInfo(file_build_path(DATA_DIRECTORY, $module, "info.php"));
            
            list( $this->name,
                  $this->description,
                  $this->author,
                  $this->futureVersion,
                  $this->futureSoftDependencies,
                  $this->futureHardDependencies ) = $this->getInfo(file_build_path(INSTALL_DATA_DIRECTORY, $module, "info.php"));

            // TODO(Matthew): Present this error to user not to backend.
            //                  For simplicity, require user deletes/repairs this module and then restart total installation process.
            if (!$this->futureVersion) {
                throw new MalformedInfoException("Could not load information of module, " . $module . ", to be installed or updated.");
            }
        }
    
        /**
         * @return string
         */
        public function getId() {
            return $this->tags[0];
        }
    
        /**
         * @param string $tag
         */
        public function addTag($tag) {
            $this->tags[] = $tag;
        }
    
        /**
         * @param string $tag
         *
         * @return bool
         */
        public function hasTag($tag) {
            return in_array($tag, $this->tags);
        }
        
        /**
         * @return string
         */
        public function getModuleName() : string {
            return $this->getId();
        }
        
        /**
         * @return string
         */
        public function getCurrentVersion() : string {
            return $this->currentVersion;
        }
        
        /**
         * @return bool
         */
        public function isInstalled() : bool {
            return (bool) $this->getCurrentVersion();
        }
        
        /**
         * @return array
         */
        public function getCurrentSoftDependencies() : array {
            return $this->currentSoftDependencies;
        }
        
        /**
         * @return array
         */
        public function getCurrentHardDependencies() : array {
            return $this->currentHardDependencies;
        }
        
        /**
         * @return string
         */
        public function getFutureVersion() : string {
            return $this->futureVersion;
        }
        
        /**
         * @return array
         */
        public function getFutureSoftDependencies() : array {
            return $this->futureSoftDependencies;
        }
        
        /**
         * @return array
         */
        public function getFutureHardDependencies() : array {
            return $this->futureHardDependencies;
        }
        
        /**
         * Gets the future version if the module is to be updated, or the current version.
         *
         * @return string
         */
        public function getLatestVersion() : string {
            $futureVersion = $this->getFutureVersion();
            if (!$futureVersion) {
                return $this->getCurrentVersion();
            }
            return $futureVersion;
        }
        
        /**
         * Gets the future soft dependencies if the module is to be updated, or the current version.
         *
         * @return array
         */
        public function getLatestSoftDependencies() : array {
            $futureDependencies = $this->getFutureSoftDependencies();
            if (!$futureDependencies) {
                return $this->getCurrentSoftDependencies();
            }
            return $futureDependencies;
        }
        
        /**
         * Gets the future hard dependencies if the module is to be updated, or the current version.
         *
         * @return array
         */
        public function getLatestHardDependencies() : array {
            $futureDependencies = $this->getFutureHardDependencies();
            if (!$futureDependencies) {
                return $this->getCurrentHardDependencies();
            }
            return $futureDependencies;
        }
        
        // TODO(Matthew): Handle poorly formed info files in such a way as to present to user rather than quietly fail.
        /**
         * Gets the module information provided by the PHP array at the given filepath.
         *
         * @param string $filepath
         *
         * @return array Array of the version and dependencies of the information
         *
         * @throws \Alder\Install\Info\Exception\MalformedInfoException
         */
        protected function getInfo(string $filepath) : array {
            $info = include $filepath;
            
            if (!$info) {
                return [
                    "",
                    "",
                    "",
                    "",
                    [],
                    []
                ];
            }
            
            // Ensure a well-formed module name is provided.
            if (!is_string($name = $info["name"] ?? null) || !$name) {
                throw new MalformedInfoException("The name information provided for the module, " . $filepath . ", is malformed.");
            }

            // If provided, ensure module description is well-formed.
            $description = $info["description"] ?? "";
            if (!is_string($description)) {
                throw new MalformedInfoException("The description information provided for the module, " . $filepath . ", is malformed.");
            }

            // If provided, ensure module description is well-formed.
            $author = $info["author"] ?? "";
            if (!is_string($author)) {
                throw new MalformedInfoException("The author information provided for the module, " . $filepath . ", is malformed.");
            }

            // Ensure the information provided has a well-formed version string, set the Info instance's version field.
            if (!($version = $this->versionProvided($info))) {
                throw new MalformedInfoException("The version information provided for the specified module, " . $filepath . ", is malformed.");
            }
            
            // Ensure a well-formed soft dependency array is provided.
            if (!is_array($softDependencies = $info["soft_dependencies"] ?? [])) {
                throw new MalformedInfoException("The soft dependencies information provided for the specified module, " . $filepath . ", is malformed.");
            }
            
            // Ensure a well-formed hard dependency array is provided.
            if (!is_array($hardDependencies = $info["hard_dependencies"] ?? [])) {
                throw new MalformedInfoException("The hard dependencies information provided for the specified module, " . $filepath . ", is malformed.");
            }

            return [
                $name,
                $description,
                $author,
                $version,
                $softDependencies,
                $hardDependencies
            ];
        }
        
        /**
         * Determines if a version string that is validly formatted exists in the "version" field of the provided array.
         *
         * @param array $info
         *
         * @return bool|string
         */
        protected function versionProvided(array $info) {
            return isset($info["version"]) && is_string($info["version"])
                   && preg_match(
                       "/^([0-9]+\.)+[0-9]+$/",
                       $info["version"]
                   ) ? $info["version"] : false;
        }
    }
