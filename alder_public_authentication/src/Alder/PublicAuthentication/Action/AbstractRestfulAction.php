<?php

    namespace Alder\PublicAuthentication\Action;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

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
        protected function get($id) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to acquire a set of specific resources.
         * 
         * @abstract
         */
        protected function getList() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to create a new resource.
         * 
         * @abstract
         */
        protected function create() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to update a specific resource.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to update.
         */
        protected function update($id) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to update a set of resources.
         * 
         * @abstract
         */
        protected function updateList() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to replace a specific resource.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to replace.
         */
        protected function replace($id) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to replace a set of resources.
         * 
         * @abstract
         */
        protected function replaceList() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to delete a specific resource.
         * 
         * @abstract
         */
        protected function delete($id) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to delete a set of resources.
         * 
         * @abstract
         * 
         * @param string The ID of the resource to delete.
         */
        protected function deleteList() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request for the options related to the route path of the request.
         * 
         * @abstract
         */
        protected function options() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Determines the appropriate action function to call for the request method and parameters.
         * 
         * @param ServerRequestInterface $request The request object.
         * @param ResponseInterface $response The response object.
         * @param callable $next The next middleware to be called.
         * 
         * @return NULL|Psr\Http\Message\ResponseInterface
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
        {
            $this->request = $request;
            $this->response = $response;
            
            $method = strtoupper($this->request->getMethod());
            switch ($method) {
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
                    $this->response = $this->response->withBody(NULL);
                default:
                    $this->response = $this->response->withStatus(405);
            }

            if ($next) {
                return $next($this->request, $this->response);
            }
            return $this->response;
        }
        
        /**
         * Fetches the named parameter from the route matching first, and if nothing is found in the route, from
         * the parsed body if method type is POST, else from the query string. Returns NULL if nothing is found.
         * 
         * @param string $parameterHandle The handle of the parameter to be fetched.
         * 
         * @return string The parameter fetched.
         */
        protected function getParameter($parameterHandle) {
            $params = $this->request->getQueryParams();
            if (isset($params["$parameterHandle"])) {
                $param = $params["$parameterHandle"];
            }

            $parsedBody = $this->request->getParsedBody();
            if ($parsedBody && isset($parsedBody["$parameterHandle"])) {
                $param = $parsedBody["$parameterHandle"];
            }
            
            return $this->request->getAttribute($parameterHandle, $param ?: NULL);
        }

//        /**
//         * Determines if the request header content type handle is a valid handle of the given content type.
//         *
//         * @param type $contentType The content type to check for.
//         *
//         * @return boolean True if the given content type is the request's content type, false otherwise.
//         */
//        protected function bodyIsContentType($contentType)
//        {
//            $headerContentType = $this->request->getHeader("content-type");
//
//            if (empty($headerContentType)) {
//                return false;
//            }
//
//            if (array_key_exists($contentType, self::CONTENT_TYPES)) {
//                foreach (self::CONTENT_TYPES[$contentType] as $contentTypeHandle) {
//                    if (stripos($contentTypeHandle, $headerContentType[0]) == 0) {
//                        return true;
//                    }
//                }
//            }
//
//            return false;
//        }
        
//        /**
//         * Processes the body content of the request.
//         *
//         * @return object|string|array
//         */
//        protected function processBodyContent()
//        {
//            // TODO(Matthew): This is a stream, and body could be HUGE, so we should be checking size and rejecting bodies of ridiculous size. Should be done at earlier stage, e.g. at pre-routing stage.
//            $body = (string) $this->request->getBody();
//
//            if ($this->bodyIsContentType($this->request, "json")) {
//                return Json::decode($body, Json::TYPE_ARRAY);
//            }
//
//            parse_str($body, $parsedBody);
//
//            if (!is_array($parsedBody) || empty($parsedBody) ||
//                    (count($parsedBody) == 1 && $parsedBody[0] == "")) {
//                return $body;
//            }
//
//            return $parsedBody;
//        }
    }
