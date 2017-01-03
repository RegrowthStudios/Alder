<?php
    
    namespace Alder\Install\Marshaller;
    
    use Alder\Install\Info\Cache;
    
    use MikeRoetgers\DependencyGraph\DependencyManager;
    
    class Marshaller
    {
        public static function marshalInstallAndUpgradeActions() {
            $dependencyManager = new DependencyManager();
            
            foreach (new \DirectoryIterator(INSTALL_DATA_DIRECTORY) as $file) {
                if ($file->isDir()) {
                    self::prepareModuleForMarshalling($file->getBasename(), $dependencyManager);
                }
            }
        }
        
        protected static function prepareModuleForMarshalling(string $module, DependencyManager& $dependencyManager) {
            $moduleInfo = Cache::getInfoObj($module);
            
            $evaluation = $moduleInfo->evaluateDependencies();
            if (!$evaluation["_dependenc"]) {
            
            }
        }
    }
