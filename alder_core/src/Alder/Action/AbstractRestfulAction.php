<?php
    
    namespace Alder\Action;
    
    use Alder\ApiMap\Factory as ApiMapFactory;
    use Alder\Middleware\MiddlewareTrait;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Response\JsonResponse;
    use Zend\Diactoros\Stream;
    use Zend\Json\Json;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * Provides abstract functionality for actions, such as routing by request method.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     * @abstract
     */
    abstract class AbstractRestfulAction implements MiddlewareInterface
    {
        use MiddlewareTrait;
        
        protected $metadata;
        
        // TODO(Matthew): Add more details on how these functions may satisfy the related RFCs.
        /**
         * Processes a request to acquire a specific resource.
         */
        protected function get() : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to create a new resource.
         */
        protected function create() : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to update a specific resource.
         */
        protected function update() : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to replace a specific resource.
         */
        protected function replace() : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to delete a specific resource.
         */
        protected function delete() : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request for the options related to the route path of the request.
         */
        protected function options() : void {
            $canonicalAction = canonicalise_action(get_class($this));
            
            $apiMap = ApiMapFactory::create();
            
            if (!isset($metadata["module"]) || !isset($apiMap[$metadata["module"]][$canonicalAction])) {
                $this->response = $this->response->withStatus(405, "Method Not Allowed");
                
                return;
            }
            
            $actionMap = $apiMap[$metadata["module"]][$canonicalAction];
            $this->response = (new JsonResponse($actionMap["body"]))->withHeader("Allow", $actionMap["allow"]);
        }
        
        /**
         * Processes a HEAD request to a specific resource.
         */
        protected function head() : void {
            $this->get();
            
            $bodySize = $this->response->getBody()->getSize();
            $this->response = $this->response->withBody(new Stream(""))->withHeader("Content-Length", $bodySize);
        }
        
        // TODO(Matthew): May be more sensible to require JSON content-type requests instead of x-www-form-url-encoded.
        // TODO(Matthew): Should move the JSON decoding to its own function to handle potential exceptions.
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
            
            $method = strtoupper($this->request->getMethod());
            switch ($method) {
                case "GET":
                    $this->get();
                    break;
                case "POST":
                    $this->create();
                    break;
                case "PATCH":
                    $this->update();
                    break;
                case "PUT":
                    $this->replace();
                    break;
                case "DELETE":
                    $this->delete();
                    break;
                case "OPTIONS":
                    $this->options();
                    break;
                case "HEAD":
                    $this->head();
                    break;
                default:
                    $this->response = $this->response->withStatus(405);
            }
            
            // if ($next && $this->response === $response) {
            if ($next) {
                return $next($this->request, $this->response);
            }
            
            return $this->response;
        }
        
        /**
         * Prepares the action with necessary metadata about itself.
         *
         * @param array $metadata
         */
        public function __construct(array $metadata) {
            $this->metadata = $metadata;
        }
    }
