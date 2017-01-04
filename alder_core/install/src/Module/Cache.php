<?php
    
    namespace Alder\Install\Module;
    
    use Alder\Install\Info\Exception\MalformedInfoException;
    
    class Cache
    {
        protected static $infos = [];
        
        public static function getModule(string $moduleName) : Module {
            self::normaliseModuleName($moduleName);
            
            if (isset(self::$infos[$moduleName])) {
                return self::$infos[$moduleName];
            }
            
            try {
                return self::$infos[$moduleName] = new Module($moduleName);
            } catch (MalformedInfoException $exception) {
                throw $exception;
            }
        }
        
        /**
         * Normalises the provided module name.
         * E.g. from "public_authentication" to "PublicAuthentication"
         *
         * @param string $name
         */
        protected static function normaliseModuleName(string& $name) {
            if (strpos($name, "_") !== false) {
                $name = ucfirst(
                    preg_replace_callback(
                        "/_([a-z])/",
                        function ($match) {
                            return strtoupper($match[1]);
                        },
                        $name
                    )
                );
            }
        }
    }
