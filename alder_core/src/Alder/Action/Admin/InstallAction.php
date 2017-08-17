<?php
    
    namespace Alder\Action\Admin;

    use \Alder\DiContainer;
    use \Alder\Install\Evaluator\Evaluator;
    use \Alder\Install\Module\Cache;
    use \Alder\Install\Verifier\Verifier;

    use \MikeRoetgers\DependencyGraph\DependencyManager;
    use \MikeRoetgers\DependencyGraph\Exception\CycleException;
    
    use \Zend\Diactoros\Response\HtmlResponse;

    /**
     * The install action for providing overview and completion views, and starting installation.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class InstallAction
    {
        /**
         * The request sent by the client.
         *
         * @var \Psr\Http\Message\ServerRequestInterface
         */
        protected $request;
        
        /**
         * The response to be delivered to the request sender.
         *
         * @var \Psr\Http\Message\ResponseInterface
         */
        protected $response;

        /**
         * Determines the appropriate action function to call for the request method and parameters.
         *
         * @param \Psr\Http\Message\ServerRequestInterface $request  The request object.
         * @param \Psr\Http\Message\ResponseInterface      $response The response object.
         * @param callable                                 $next     The next middleware to be called.
         *
         * @return NULL|\Psr\Http\Message\ResponseInterface
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ?ResponseInterface {
            $this->request = $request;
            $this->response = $response;

            switch (strtoupper($this->request->getMethod())) {
                case "GET":
                    showBegin();
                    break;
                case "POST":
                    beginInstall();
                    break;
                default:
                    $this->response = $this->response->withStatus(405);
            }

            if ($next) {
                return $next($this->request, $this->response);
            }
            
            return $this->response;
        }

        protected function showBegin() : void {
            $loader = DiContainer::get()->get("admin_template_loader");

            $template = $loader->load("install_overview.twig");

            $this->response = new HtmlResponse($template->render(getOverviewData()));
        }

        /**
         * Performs first actions in installation procedure:
         *   Replaces all files shared between old and new versions of component.
         *   Copies over unshared new files.
         *   Deletes unshared old files.
         */
        protected function beginInstall() : void {

        }

        /**
         * Figures out what data to provide for the overview.
         *
         * @return array The data to be displayed in the overview.
         */
        protected function getOverviewData() : array {
            // Returns an array of admin notifications (e.g. alerts, messages and tasks).
            $data = DiContainer::get()->get("admin_info");
            
            $dependencyManager = new DependencyManager();

            // Evaluate satisfiability of dependencies.
            list ( $allDependenciesSatisfied,
                    $dependencyEvaluations ) = Evaluator::doEvaluation($dependencyManager);

            if (empty($dependencyEvaluations)) {
                $data["no_pending_installs"] = true;
                return $data;
            }
            
            if (!$allDependenciesSatisfied) {
                $data["dependency_evaluations"] = $dependencyEvaluations;
                return $data;
            }
            
            try {
                if (empty($dependencyManager->getExecutableOperations())) {
                    throw new CycleException();
                }
            } catch (CycleException $exception) {
                // TODO(Matthew): Can we show which modules have the circular dependency?
                $data["circular_dependencies"] = true;
                return $data;
            }

            // Validate to-be-installed/updated modules.
            list ( $allValid,
                    $verificationResults ) = Verifier::verifyInstallable();

            if (!$allValid) {
                $data["verification_results"] = $verificationResults;
                return $data;
            }

            $data["modules"] = $this->listModules();
            return $data;
        }

        /**
         * Lists all modules installed and to-be-installed.
         *
         * @return array The list of modules.
         */
        protected function listModules() : array {
            cacheModulesInDir(DATA_DIRECTORY);
            cacheModulesInDir(INSTALL_DATA_DIRECTORY);

            $moduleData = [];

            foreach (Cache::getCachedModules() as $module) {
                $moduleData[] = [
                    "url" => $module->getUrl(),
                    "name" => $module->getName(),
                    "old_version" => $module->getCurrentVersion(),
                    "new_version" => $module->getLatestVersion(),
                    "author_url" => $module->getAuthorUrl(),
                    "author" => $module->getAuthor()
                ];
            }

            return $moduleData;
        }

        /**
         * Looks for modules in the specified directory and caches them.
         *
         * @param string $directory The directory in which to search for modules to cache.
         */
        protected function cacheModulesInDir(string $directory) : void {
            foreach (\DirectoryIterator($directory) as $file) {
                // TODO(Matthew): isDir may return true for dots... handle that! (Don't forget the other foreach over DirIterators...)
                if (!$file->isDir() || $file->isDot()) {
                    continue;
                }

                Cache::getModule($file->getBasename());
            }
        }
    }
