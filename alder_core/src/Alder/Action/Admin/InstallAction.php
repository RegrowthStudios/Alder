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
            $dependencyManager = new DependencyManager();

            // Evaluate satisfiability of dependencies.
            list ( $allDependenciesSatisfied,
                   $dependencyEvaluations ) = Evaluator::doEvaluation($dependencyManager);

            if (empty($dependencyEvaluations)) {
                // TODO(Matthew): Show no pending install/update view.
            }
            
            if (!$allDependenciesSatisfied) {
                // TODO(Matthew): Show error view for components whose dependencies are not satisfied.
            }

            try {
                $dependencyManager->getExecutableOperations();
            } catch (CycleException $exception) {
                // TODO(Matthew): Show error view for circular dependencies.
            }

            // Validate to-be-installed/updated modules.
            list ( $allValid,
                   $results ) = Verifier::verifyInstallable();

            if (!$allValid) {
                // TODO(Matthew): Show error view for components whose files don't match their manifest.
            }

            $data = DiContainer::get()->get("admin_info"); // Returns an array of alerts, messages and tasks.
            $data["modules"] = $this->listModules();

            $loader = DiContainer::get()->get("admin_template_loader");

            $template = $loader->load("install_overview.twig");

            $this->response = new HtmlResponse($template->render($data));
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
