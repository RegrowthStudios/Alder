<?php
    
    namespace Alder\Install\Marshaller;
    
    use Alder\Install\Evaluator\Evaluator;
    use Alder\Install\Module\Cache;
    use Alder\Install\Module\Module;
    
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
         *
         * @param string $stage The stage of installation/upgrade to perform.
         */
        public static function marshalInstallationsAndUpgrades(string $stage) {
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
                        $moduleClass  = "Alder\\Admin\\Install\\Modules\\$moduleName\\Operation\\Upgrade";
                        $defaultClass = "Alder\\Admin\\Install\\Operation\\Upgrade";
                    } else {
                        // Install
                        $moduleClass  = "Alder\\Admin\\Install\\Modules\\$moduleName\\Operation\\Install";
                        $defaultClass = "Alder\\Admin\\Install\\Operation\\Install";
                    }
                    
                    // TODO(Matthew): Handle failure case of run().
                    if (class_exists($moduleClass) && method_exists($moduleClass, $stage)) {
                        $moduleClass::$stage($moduleName);
                    }  else {
                        $defaultClass::$stage($moduleName);
                    }
                    
                    $dependencyManager->markAsExecuted($module);
                }
            }
        }
    }
