<?php
    
    namespace Alder\Action\Admin;

    use \Alder\DiContainer;
    use \Alder\Install\Verifier\Verifier;

    use \Zend\Diactoros\Response\HtmlResponse;

    /**
     * The install complete action for showing complete installation view.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class InstallCompleteAction
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
                    showComplete();
                    break;
                default:
                    $this->response = $this->response->withStatus(405);
            }

            if ($next) {
                return $next($this->request, $this->response);
            }
            
            return $this->response;
        }

        protected function showComplete() : void {
            // Verify installation.
            list ( $allValid,
                   $results ) = Verifier::verifyInstalled();

            if (!$allValid) {
                // TODO(Matthew): Show error view for components whose files don't match their manifest.
            }

            $loader = DiContainer::get()->get("admin_template_loader");

            $template = $loader->load("install_complete.twig");

            $this->response = new HtmlResponse($template->render([
                // Add parameters to be rendered.
            ]));
        }
    }
