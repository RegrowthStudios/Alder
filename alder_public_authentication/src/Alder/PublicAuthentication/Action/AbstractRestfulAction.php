<?php

    namespace Alder\PublicAuthentication\Action;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Json\Json;
    use Zend\Stratigility\MiddlewareInterface;

    /**
     * The user license action middleware for Alder's public authentication service.
     * Handles user-license-entity actions based on request and session information.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractRestfulAction implements MiddlewareInterface
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
         * Constant of content types.
         * 
         * @var array
         */
        const CONTENT_TYPES = [
            "json" => [
                "application/hal+json",
                "application/json",
                "text/x-json" // Not valid, but legacy software may use this. Better safe than sorry.
            ]
        ];
        
        // TODO(Matthew): Add more details on how these functions may satisfy the related RFCs.
        /**
         * Processes a request to acquire a specific resource.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to fetch.
         */
        abstract function get($id);
        /**
         * Processes a request to acquire a set of specific resources.
         * 
         * @abstract
         */
        abstract function getList();
        /**
         * Processes a request to create a new resource.
         * 
         * @abstract
         */
        abstract function create();
        /**
         * Processes a request to update a specific resource.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to update.
         */
        abstract function update($id);
        /**
         * Processes a request to update a set of resources.
         * 
         * @abstract
         */
        abstract function updateList();
        /**
         * Processes a request to replace a specific resource.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to replace.
         */
        abstract function replace($id);
        /**
         * Processes a request to replace a set of resources.
         * 
         * @abstract
         */
        abstract function replaceList();
        /**
         * Processes a request to delete a specific resource.
         * 
         * @abstract
         */
        abstract function delete($id);
        /**
         * Processes a request to delete a set of resources.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to delete.
         */
        abstract function deleteList();
        /**
         * Processes a request for the options related to the route path of the request.
         * 
         * @abstract
         */
        abstract function options();
        
        /**
         * Determines the appropriate action function to call for the request method and parameters.
         * 
         * @param ServerRequestInterface $request The request object.
         * @param ResponseInterface $response The response object.
         * @param callable $next The next middleware to be called.
         */
        public function __invoke(ServerRequestInterface& $request, ResponseInterface& $response, \callable $next = null)
        {
            $this->request = $request;
            $this->response = $response;
            
            $method = strtoupper($request->getMethod());
            switch($method) {
                case "GET":
                    $id = $this->getParameter("id");
                    if ($id === NULL) {
                        $this->getList();
                    }
                    $this->get($id);
                case "POST":
                    $this->create();
                case "PATCH":
                    $id = $this->getParameter("id");
                    if ($id === NULL) {
                        $this->updateList();
                    }
                    $this->update($id);
                case "PUT":
                    $id = $this->getParameter("id");
                    if ($id === NULL) {
                        $this->replaceList();
                    }
                    $this->replace($id);
                case "DELETE":
                    $id = $this->getParameter("id");
                    if ($id === NULL) {
                        $this->deleteList();
                    }
                    $this->delete($id);
                case "OPTIONS":
                    $this->options();
                case "HEAD":
                    $this->head($this->getParameter("id"));
                    $response = $response->withBody(NULL);
                default:
                    $response = $response->withStatus(405);
            }
            
            return $response;
        }
        
        /**
         * Fetches the named parameter from the route matching first, and if nothing is found in the route, from
         * the query string. Returns NULL if nothing is found.
         * 
         * @param string $parameterHandle The handle of the parameter to be fetched.
         * 
         * @return string The parameter fetched.
         */
        protected function getParameter($parameterHandle)
        {
            $params = $this->request->getQueryParams();
            if (isset ($params["$parameterHandle"])) {
                $param = $params["$parameterHandle"];
            }
            
            $param = $this->request->getAttribute($parameterHandle, $param ?: NULL);
            
            return $param;
        }
        
        /** TODO(Matthew): Below functions from Zend-MVC. Should adapt further from original imlementation or figure out how to credit appropriately. **/
        
        /**
         * Determines if the request header content type handle is a valid handle of the given content type.
         * 
         * @param ServerRequestInterface $request The request object.
         * @param type $contentType The content type to check for.
         * 
         * @return boolean True if the given content type is the request's content type, false otherwise.
         */
        protected function bodyIsContentType(ServerRequestInterface $request, $contentType)
        {
            $headerContentType = $request->getHeader("content-type");
            
            if (empty($headerContentType)) {
                return false;
            }
            
            if (array_key_exists($contentType, self::CONTENT_TYPES)) {
                foreach (self::CONTENT_TYPES[$contentType] as $contentTypeHandle) {
                    if (stripos($contentTypeHandle, $headerContentType[0]) == 0) {
                        return true;
                    } 
                }
            }
            
            return false;
        }
        
        /**
         * Processes the body content of the request.
         * 
         * @return object|string|array
         */
        protected function processBodyContent()
        {
            // TODO(Matthew): This is a stream, and body could be HUGE, so we should be checking size and rejecting bodies of ridiculous size.
            //                Should be done at earlier stage, e.g. at pre-routing stage.
            $body = (string) $this->request->getBody();
            
            if ($this->bodyIsContentType($request, "json")) {
                return Json::decode($body, Json::TYPE_ARRAY);
            }
            
            parse_str($body, $parsedBody);
            
            if (!is_array($parsedBody) || empty($parsedBody) ||
                    (count($parsedBody) == 1 && $parsedBody[0] == "")) {
                return $body;
            }
            
            return $parsedBody;
        }
    }
