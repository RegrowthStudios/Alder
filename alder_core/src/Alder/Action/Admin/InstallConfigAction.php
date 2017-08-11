<?php
    
    namespace Alder\Action\Admin;
    
    /**
     * Handles showing config options view and applying the chosen options.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class InstallConfigAction
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
                    showConfigOptions();
                    break;
                case "POST":
                    applyConfigChoices();
                    break;
                default:
                    $this->response = $this->response->withStatus(405);
            }

            if ($next) {
                return $next($this->request, $this->response);
            }
            
            return $this->response;
        }

        protected function showConfigOptions() : void {

        }

        protected function applyConfigChoices() : void {

        }
    }
