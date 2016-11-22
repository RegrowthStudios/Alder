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
         *
         * @param mixed $data Data from request.
         */
        protected function get($data) : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to create a new resource.
         *
         * @param mixed $data Data from request.
         */
        protected function create($data) : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to update a specific resource.
         *
         * @param mixed $data Data from request.
         */
        protected function update($data) : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to replace a specific resource.
         *
         * @param mixed $data Data from request.
         */
        protected function replace($data) : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to delete a specific resource.
         *
         * @param mixed $data Data from request.
         */
        protected function delete($data) : void {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request for the options related to the route path of the request.
         */
        protected function options() : void {
            $action = canonicalise_action_class_path(get_class($this));
            
            if ($action === null) {
                $this->response = $this->response->withStatus(405, "Method Not Allowed");
                
                return;
            }
            
            $apiMap = ApiMapFactory::create();
            
            if (!isset($metadata["module"]) && !isset($apiMap[$metadata["module"]][$action])) {
                $this->response = $this->response->withStatus(405, "Method Not Allowed");
                
                return;
            }
            
            $actionMap = $apiMap[$metadata["module"]][$action];
            $this->response = (new JsonResponse($actionMap["body"]))->withHeader("Allow", $actionMap["allow"]);
        }
        
        /**
         * Processes a HEAD request to a specific resource.
         *
         * @param mixed $data Data from request.
         */
        protected function head($data) : void {
            $this->get($data);
            
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
                    $this->get(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                case "POST":
                    $this->create(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                case "PATCH":
                    $this->update(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                case "PUT":
                    $this->replace(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                case "DELETE":
                    $this->delete(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                case "OPTIONS":
                    $this->options();
                    break;
                case "HEAD":
                    $this->head(Json::decode($this->getParameter("data"), Json::TYPE_ARRAY));
                    break;
                default:
                    $this->response = $this->response->withStatus(405);
            }
            
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