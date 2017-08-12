<?php
    
    namespace Alder\Install\Module;
    
    use Alder\Install\Module\Exception\MalformedInfoException;
    
    class Cache
    {
        protected static $infos = [];
        
        public static function getModule(string $moduleName) : Module {
            if (isset(self::$infos[$moduleName])) {
                return self::$infos[$moduleName];
            }
            
            try {
                return self::$infos[$moduleName] = new Module($moduleName);
            } catch (MalformedInfoException $exception) {
                throw $exception;
            }
        }

        public static function getCachedModules() : array {
            return self::$infos;
        }
    }
