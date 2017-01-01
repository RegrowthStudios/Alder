<?php
    
    namespace Alder\Install\Info;
    
    use Alder\Install\Info\Exception\MalformedInfoException;
    
    class Cache
    {
        protected static $infos = [];
        
        public static function getInfoObj(string $module) {
            if (isset(self::$infos[$module])) {
                return self::$infos[$module];
            }
            
            try {
                return self::$infos[$module] = new Info($module);
            } catch (MalformedInfoException $exception) {
                throw $exception;
            }
        }
    }
