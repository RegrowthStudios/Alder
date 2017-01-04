<?php
    
    namespace Alder\Install\Info;
    
    use Alder\Install\Info\Exception\MalformedInfoException;
    
    class Cache
    {
        protected static $infos = [];
        
        public static function getInfoObj(string $module) : Info {
            self::normaliseModuleName($module);
            
            if (isset(self::$infos[$module])) {
                return self::$infos[$module];
            }
            
            try {
                return self::$infos[$module] = new Info($module);
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
