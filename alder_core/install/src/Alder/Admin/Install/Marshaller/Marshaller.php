<?php
    
    namespace Alder\Admin\Install\Marshaller;
    
    use Alder\Admin\Install\Evaluator\Evaluator;
    use Alder\Admin\Install\Module\Cache;
    use Alder\Admin\Install\Module\Module;
    
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
            
            // Prepare each module for marshalling by evaluation all depedencies and constructing a dependency graph..
            list ( $netEvaluation,
                   $evaluations ) = Evaluator::doEvaluation($dependencyManager);
            
            if (!$netEvaluation) {
                // TODO*Matthew): Handle unresolvable dependency requirements.
                //                Print evaluations in a view.
            }

            // Iterate over all possible executable operations at a given point, until we execute all
            // operations required by the installation/upgrade process.
            /**
             * @var \Alder\Install\Module\Module[] $updatableModules
             */
            while ($updatableModules = $dependencyManager->getExecutableOperations()) {
                while ($module = array_shift($updatableModules)) {
                    $dependencyManager->markAsStarted($module);

                    if (!$module->hasUpdate) {
                        $dependencyManager->markAsExecuted($module);
                        continue;
                    }
                    
                    $moduleClass  = "";
                    $defaultClass = "";

                    $moduleName = $module->getModuleName();
                    if ($module->getCurrentVersion()) {
                        // Upgrade
                        $moduleClass  = "Alder\\Install\\Modules\\$moduleName\\Action\\Upgrade";
                        $defaultClass = "Alder\\Install\\Action\\Upgrade";
                    } else {
                        // Install
                        $moduleClass  = "Alder\\Install\\Modules\\$moduleName\\Action\\Install";
                        $defaultClass = "Alder\\Install\\Action\\Install";
                    }
                    
                    // TODO(Matthew): Handle failure case of run().
                    if (method_exists($moduleClass, "run")) {
                        $moduleClass::run($moduleName);
                    } else {
                        $defaultClass::run($moduleName);
                    }
                    
                    $dependencyManager->markAsExecuted($module);
                }
            }
        }
    }
