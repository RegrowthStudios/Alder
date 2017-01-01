<?php
    
    namespace Alder\Install\Info;
    
    use Alder\Install\Info\Exception\MalformedInfoException;
    
    class Info
    {
        protected $version = "";
        
        protected $dependencies = [];
        
        public function __construct(string $module) {
            if (strpos($module, "_") !== false) {
                $module = ucfirst(preg_replace_callback("/_([a-z])/", function ($match) {
                    return strtoupper($match[1]);
                }, $module));
            }
            
            // Determine if the provided module has an info file, if so move info to memory.
            $infoFilepath = file_build_path(INSTALL_DATA_DIRECTORY, $module, "info.php");
            if (!is_readable($infoFilepath)) {
                throw new \InvalidArgumentException("No information provided for the specified module, or the specified module does not exist.");
            }
            $info = require_once $infoFilepath;
            
            // Determine if the information provided has a well-formed version string, if so set the Info instance's version field.
            if (!isset($info["version"]) || !is_string($info["version"]) || !preg_match("/^([0-9]+\.)+[0-9]+$/", $info["version"])) {
                throw new MalformedInfoException("The information provided for the specified module is malformed.");
            }
            $this->version = $info["version"];
            
        }
        
        public function dependenciesWillBeMet() {
        
        }
    }
