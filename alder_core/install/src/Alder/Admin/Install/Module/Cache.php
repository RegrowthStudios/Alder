<?php
    
    namespace Alder\Admin\Install\Module;
    
    use Alder\Admin\Install\Module\Exception\MalformedInfoException;
    
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
    }
