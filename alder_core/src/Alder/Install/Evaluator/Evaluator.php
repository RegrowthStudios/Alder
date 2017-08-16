<?php
    
    namespace Alder\Install\Evaluator;
    
    use Alder\Install\Module\Cache;
    use Alder\Install\Module\Module;
    
    use Composer\Semver\Semver;
    
    use MikeRoetgers\DependencyGraph\DependencyManager;
    
    /**
     * Handles evaluating all dependencies and constructing a graph of installed, installable and updatable components.
     * 
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0 
     */
    class Evaluator
    {
        public static function doEvaluation(DependencyManager& $dependencyManager) : array {
            list ( $installDirNetEvaluation,
                   $installDirEvaluations ) = static::evaluateDependenciesInDir(INSTALL_DATA_DIRECTORY, $dependencyManager);

            list ( $liveDirNetEvaluation,
                   $liveDirEvaluations ) = static::evaluateDependenciesInDir(DATA_DIRECTORY, $dependencyManager);
            
            return [
                $installDirNetEvaluation && $liveDirNetEvaluation,
                array_merge($installDirEvaluations, $liveDirEvaluations)
            ];
        }

        protected static function evaluateDependenciesInDir(string $directory, DependencyManager& $dependencyManager) : array {
            $netEvaluation = true;
            $evaluations   = [];

            foreach (\DirectoryIterator($directory) as $file) {
                if ($file->isDir() && !$file->isDot()) {
                    $result = static::evaluateDependencies($file->getBasename(), $dependencyManager);
                    if ($result == null) {
                        continue;
                    }

                    $netEvaluation                     = $netEvaluation && $result[0];
                    $evaluations[$file->getBasename()] = $result[1];
                }
            }

            return [
                $netEvaluation,
                $evaluations
            ];
        }

        protected static function evaluateDependencies(string $moduleName, DependencyManager& $dependencyManager) : ?array {
            $module = Cache::getModule($moduleName);

            if (isset($dependencyManager->getOperations()[$module->getId()])) {
                return null;
            }

            $dependencyManager->addOperation($module);

            list ( $softNetEvaluation,
                   $softEvaluations ) = static::evaluateDependencySet($module);
            list ( $hardNetEvaluation,
                   $hardEvaluations ) = static::evaluateDependencySet($module, $dependencyManager);
        
            $netEvaluation = $softNetEvaluation && $hardEvaluations;
            $evaluations   = array_merge($softEvaluations, $hardEvaluations);

            return [ $netEvaluation, $evaluations ];
        }

        protected static function evaluateDependencySet(Module& $module, DependencyManager& $dependencyManager = null) : array {
            $netEvaluation = true;
            $evaluations   = [];
            
            if ($dependencyManager) {
                $dependencies = $module->getLatestHardDependencies();
            } else {
                $dependencies = $module->getLatestSoftDependencies();
            }
            
            foreach ($dependencies as $dependencyName => $constraint) {
                $dependency = Cache::getModule($dependencyName);

                $evaluations[$dependencyName] = [
                    "current_version" => $dependency->getCurrentVersion(),
                    "future_version"  => $dependency->getFutureVersion()
                ];

                if (!Semver::satisfies($dependency->getLatestVersion(), $constraint)) {
                    $netEvaluation                              = false;
                    $evaluations[$dependencyName]["evaluation"] = false;
                } else {
                    $evaluations[$dependencyName]["evaluation"] = true;

                    if ($dependencyManager) {
                        if (!isset($dependencyManager->getOperations()[$$dependency->getId()])) {
                            $dependencyManager->addOperation($dependency);
                        }
                        $dependencyManager->addDependencyByTag($dependency, $module->getId());
                    }
                }
            }

            return [ $netEvaluation, $evaluations ];
        }
    }
