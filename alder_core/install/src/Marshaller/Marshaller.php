<?php
    
    namespace Alder\Install\Marshaller;
    
    use Alder\Install\Module\Cache;
    use Alder\Install\Module\Module;
    
    use Composer\Semver\Semver;
    
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
            
            /**
             * @var \Alder\Install\Module\Module[] $executableOperations
             */
            while ($executableOperations = $dependencyManager->getExecutableOperations()) {
                while ($operation = array_shift($executableOperations)) {
                    $dependencyManager->markAsStarted($operation);
                    $moduleName = $operation->getModuleName();
                    if ($operation->getCurrentVersion()) {
                        // Updgrade
                        $class = "Alder\\Install\\Modules\\$moduleName\\Upgrade";
                        $class::run();
                    } else {
                        // Install
                        $class = "Alder\\Install\\Modules\\$moduleName\\Install";
                        $class::run();
                    }
                    $dependencyManager->markAsExecuted($operation);
                }
            }
        }
        
        protected static function prepareModuleForMarshalling(string $moduleName, DependencyManager& $dependencyManager) {
            $module = Cache::getModule($moduleName);
            
            // Evaluate soft dependencies.
            [ $netSoftEvaluation,
              $softEvaluations ] = self::evaluateDependencies($module);
            if (!$netSoftEvaluation) {
                // TODO(Matthew): Print error of each failed dependency evaluation.
            }
            
            // Evaluate soft dependencies.
            [ $netHardEvaluation,
              $hardEvaluations ] = self::evaluateDependencies($module, $dependencyManager);
            if (!$netHardEvaluation) {
                // TODO(Matthew): Print error of each failed dependency evaluation.
            }
        }
        
        /**
         * Evaluates the constraints of the dependencies of the module
         *
         * @param \Alder\Install\Module\Module $module
         * @param \MikeRoetgers\DependencyGraph\DependencyManager $dependencyManager
         *
         * @return array
         */
        protected static function evaluateDependencies(Module $module, DependencyManager& $dependencyManager = null) : array {
            $netEvaluation = true;
            $evaluations = [];
            
            if ($dependencyManager) {
                $dependencyManager->addOperation($module);
            }
            
            foreach ($module->getLatestSoftDependencies() as $dependencyName => $constraint) {
                $dependency = Cache::getModule($dependencyName);
                
                $evaluations[$dependencyName] = [
                    "current_version" => $dependency->getCurrentVersion(),
                    "future_version" => $dependency->getFutureVersion()
                ];
                if (!Semver::satisfies($dependency->getLatestVersion(), $constraint)) {
                    $netEvaluation = false;
                    $evaluations[$dependencyName]["evaluation"] = false;
                } else {
                    $evaluations[$dependencyName]["evaluation"] = true;
                    if ($dependencyManager) {
                        $dependencyManager->addOperation($dependency)
                                          ->addDependencyByTag($dependency, $module->getModuleName());
                    }
                }
            }
            
            return [ $netEvaluation, $evaluations ];
        }
    }
