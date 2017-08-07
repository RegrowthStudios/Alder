<?php
    
    namespace Alder\Admin\Install\Action;

    use \Twig_Environment;
    use \Twig_Loader_Filesystem;

    use \Zend\Expressive\Twig\TwigRenderer;
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
                    if (installComplete()) {
                        showComplete();
                    } else {
                        if (firstInstall()) {
                            installDependencies();
                        }
                        showBegin();
                    }
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

        protected function firstInstall() : bool {
            
        }

        protected function installDependencies() : void {

        }

        protected function showBegin() : void {
            // Get information about to-be-installed/updated modules.

            $loader = new Twig_Loader_Filesystem(file_build_path(PUBLIC_ADMIN_DIRECTORY, "templates"));
            $twig   = new Twig_Environment($loader, [
                "cache" => file_build_path(CACHE_DIRECTORY, "templates")
            ]);

            $template = $twig->load("install_overview.twig");

            $this->response = new HtmlResponse($template->render([
                // Add parameters to be rendered.
            ]));
        }

        protected function beginInstall() : void {

        }

        protected function installComplete() : bool {

        }

        protected function showComplete() : void {

        }
    }
