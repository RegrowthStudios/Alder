<?php
    
    namespace Alder\Install\Marshaller;
    
    use Alder\Install\Module\Cache;
    use Alder\Install\Module\Module;
    
    use Composer\Semver\Semver;
    
    use MikeRoetgers\DependencyGraph\DependencyManager;
    
    /**
     * Class that handles the marshalling of installations and upgrades.
     * 
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0 
     */
    class Marshaller
    {
        /**
         * Handles the marshalling of the actions required to complete the ongoing installations and/or upgrades.
         */
        public static function marshalInstallAndUpgradeActions() {
            $dependencyManager = new DependencyManager();
            
            // Prepare each module for marshalling by entering dependency objects into dependency manager.
            foreach (new \DirectoryIterator(INSTALL_DATA_DIRECTORY) as $file) {
                if ($file->isDir()) {
                    self::prepareModuleForMarshalling($file->getBasename(), $dependencyManager);
                }
            }
            
            // Iterate over all possible executable operations at a given point, until we execute all
            // operations required by the installation/upgrade process.
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
                    } else {
                        // Install
                        $class = "Alder\\Install\\Modules\\$moduleName\\Install";
                    }
                    
                    if (method_exists($class, "run")) {
                        $class::run();
                    } else {
                        // Error: Missing execution function.
                    }
                    
                    $dependencyManager->markAsExecuted($operation);
                }
            }
        }
        
        /**
         * Prepares the named module for marshalling.
         *
         * @param string $moduleName
         * @param \MikeRoetgers\DependencyGraph\DependencyManager $dependencyManager
         *
         * @return array|bool
         */
        protected static function prepareModuleForMarshalling(string $moduleName, DependencyManager& $dependencyManager) {
            $module = Cache::getModule($moduleName);
            
            // Evaluate our capability to satisfy soft dependencies.
            //   Soft -> we need it to run the app but not to complete this module's install/upgrade.
            [ $netSoftEvaluation,
              $softEvaluations ] = self::evaluateDependencies($module);
            if (!$netSoftEvaluation) {
                // TODO(Matthew): Print error of each failed dependency evaluation.
            }
            
            // Evaluate our capbility to satisfy hard dependencies.
            //   Hard -> we need it to not just run the app but to complete this module's install/upgrade.
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
