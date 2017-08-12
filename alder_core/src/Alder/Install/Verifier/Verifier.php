<?php

    namespace Alder\Install\Verifier;

    use Alder\Install\Module\Cache;

    use MikeRoetgers\DependencyGraph\DependencyManager;

    /**
     * Provides procedures to verify the integrity of components installed and installable.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Verifier
    {
        const NO_MANIFEST = "no_manifest";
        const NOT_VALID   = "not_valid";
        const VALID       = "valid";

        /**
         * Verifies integrity of installed components.
         * 
         * @param string $manifest The name of the manifest file.
         *
         * @return array A list of two parts: whether everything verified was valid, and an array of results per file per component.
         */
        public static function verifyInstalled(string $manifest = "files.json") : array {
            $dependencyManager = static::constructInvertedDependencyGraph();

            return static::verifyAllInDir(APP_DIRECTORY, $dependencyManager, $manifest);
        }

        /**
         * Verifies integrity of installable components.
         * 
         * @param string $manifest The name of the manifest file.
         *
         * @return array A list of two parts: whether everything verified was valid, and an array of results per file per component.
         */
        public static function verifyInstallable(string $manifest = "files.json") : array {
            $dependencyManager = static::constructInvertedDependencyGraph(true);

            return static::verifyAllInDir(INSTALL_DIRECTORY, $dependencyManager, $manifest);
        }

        /**
         * Constructs an inverted dependency graph of components to be verified.
         * 
         * @param bool $installable Whether the dependency graph is of installable or installed components.
         *
         * @return \MikeRoetgers\DependencyGraph\DependencyManager The constructed dependency graph.
         */
        protected static function constructInvertedDependencyGraph(bool $installable) : DependencyManager {
            $dependencyManager = new DependencyManager();
            
            $infoDir = $installable ? INSTALL_DATA_DIRECTORY : DATA_DIRECTORY;
            foreach (\DirectoryIterator($infoDir) as $file) {
                if (!$file->isDir()) continue;

                $name = $file->getBasename();

                $module = Cache::getModule($name);

                if (!isset($dependencyManager->getOperations()[$module->getId()])) {
                    $dependencyManager->addOperation($module);
                }

                $depndencies = $installable ? $module->getFutureHardDependencies() :
                                              $module->getCurrentHardDependencies();

                foreach ($dependencies as $dependencyName => $constraint) {
                    $dependency = Cache::getModule($dependencyName);

                    if (!isset($dependencyManager->getOperations()[$$dependency->getId()])) {
                        $dependencyManager->addOperation($dependency);
                    }

                    $dependencyManager->addDependencyByTag($module->getId(), $dependency);
                }
            }

            return $dependencyManager;
        }

        /**
         * Verifies integrity of components in the specified directory.
         * 
         * @param string $directory The name of the directory in which to verify components.
         * @param \MikeRoetgers\DependencyGraph\DependencyManager $dependencyManager The (inverted) dependency graph to use for handling overrides.
         * @param string $manifest The name of the manifest file.
         *
         * @return array A list of two parts: whether everything verified was valid, and an array of results per file per component.
         */
        protected static function verifyAllInDir(string $directory, DependencyManager $dependencyManager, string $manifest = "files.json") : array {
            $results = [];

            $filesChecked = [];
            while ($verifiableModules = $dependencyManager->getExecutableOperations()) {
                $filesCheckedThisRound = [];
                while ($module = array_shift($verifiableModules)) {
                    $dependencyManager->markAsStarted($module);

                    $moduleName = $module->getModuleName();
                    $manifestFilepath = file_build_path($directory, "data", $moduleName, $manifest);

                    if (!file_exists($manifestFilepath)) {
                        $results[$name]["result"] = NO_MANIFEST;
                        continue;
                    }

                    $manifestData = json_decode(file_get_contents($manifestFilepath));

                    foreach ($manifestData as $filepathPart => $hash) {
                        $filepath = file_build_path($directory, $filepathPart);

                        // If file has already been checked, then skip it.
                        if (in_array($filepath, $filesChecked)) continue;

                        // Check if the file exists.
                        if (!file_exists($filepath)) {
                            // If the file does not exist, check if it was deleted.
                            if ($hash == "DELETE") {
                                // File deleted, we're good.
                                unset($results[$name]["files"][$filepath]);
                                $results[$name]["files"][$filepath]["result"] = VALID;
                            } else {
                                // File wasn't deleted, check if multiple possible owning components exist.
                                if (!isset($results[$name]["files"][$filepath])) {
                                    $results[$name]["files"][$filepath]["result"] = NOT_VALID;
                                    $results[$name]["files"][$filepath]["possibleOwners"] = [
                                        $moduleName
                                    ];
                                } else if ($results[$name]["files"][$filepath]["result"] == NOT_VALID) {
                                  $results[$name]["files"][$filepath]["possibleOwners"][] = $moduleName;
                                }
                            }
                        }
                        // Check if file satisfies the hash.
                        else if ($hash == md5(file_get_contents($filepath))) {
                            // File satisfies hash, we're good.
                            unset($results[$name]["files"][$filepath]);
                            $results[$name]["files"][$filepath]["result"] = VALID;
                        } else {
                            // File doesn't satisfy hash, check if multiple possible owning components exist.
                            if (!isset($results[$name]["files"][$filepath])) {
                                $results[$name]["files"][$filepath]["result"] = NOT_VALID;
                                $results[$name]["files"][$filepath]["possibleOwners"] = [
                                    $moduleName
                                ];
                            } else if ($results[$name]["files"][$filepath]["result"] == NOT_VALID) {
                                $results[$name]["files"][$filepath]["possibleOwners"][] = $moduleName;
                            }
                        }

                        if (!in_array($filepath, $filesCheckedThisRound)) {
                            $filesCheckedThisRound[] = $filepath;
                        }
                    }

                    $dependencyManager->markAsExecuted($module);
                }
                $filesChecked = array_merge($filesChecked, $filesCheckedThisRound);
            }

            $netResult = true;
            foreach ($results as $module) {
                if ($module["result"] == NO_MANIFEST) {
                    $netResult = false;
                    continue;
                }
                $module["result"] = VALID;
                foreach ($module["files"] as $file) {
                    if ($file["result"] == NOT_VALID) {
                        $module["result"] = NOT_VALID;
                        $netResult = false;
                    }
                }
            }

            return [
                $netResult,
                $results
            ];
        }
    }
