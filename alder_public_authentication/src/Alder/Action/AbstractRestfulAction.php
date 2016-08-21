<?php

    namespace Alder\Action;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    use Zend\Diactoros\Stream;
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
         * @param mixed $data Data from request.
         *
         * @abstract
         */
        protected function get($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
//        /**
//         * Processes a request to acquire a set of specific resources.
//         *
//         * @abstract
//         */
//        protected function getList() {
//            $this->response = $this->response->withStatus(405, "Method Not Allowed");
//        }
        
        /**
         * Processes a request to create a new resource.
         *
         * @param mixed $data Data from request.
         * 
         * @abstract
         */
        protected function create($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Processes a request to update a specific resource.
         *
         * @param mixed $data Data from request.
         * 
         * @abstract
         */
        protected function update($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
//        /**
//         * Processes a request to update a set of resources.
//         *
//         * @abstract
//         */
//        protected function updateList() {
//            $this->response = $this->response->withStatus(405, "Method Not Allowed");
//        }
        
        /**
         * Processes a request to replace a specific resource.
         *
         * @param mixed $data Data from request.
         * 
         * @abstract
         */
        protected function replace($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
//        /**
//         * Processes a request to replace a set of resources.
//         *
//         * @abstract
//         */
//        protected function replaceList() {
//            $this->response = $this->response->withStatus(405, "Method Not Allowed");
//        }
        
        /**
         * Processes a request to delete a specific resource.
         *
         * @param mixed $data Data from request.
         * 
         * @abstract
         */
        protected function delete($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
//        /**
//         * Processes a request to delete a set of resources.
//         *
//         * @abstract
//         */
//        protected function deleteList() {
//            $this->response = $this->response->withStatus(405, "Method Not Allowed");
//        }
        
        /**
         * Processes a request for the options related to the route path of the request.
         * 
         * @abstract
         */
        protected function options() {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }

        /**
         * Processes a HEAD request to a specific resource.
         *
         * @param mixed $data Data from request.
         *
         * @abstract
         */
        protected function head($data) {
            $this->response = $this->response->withStatus(405, "Method Not Allowed");
        }
        
        /**
         * Determines the appropriate action function to call for the request method and parameters.
         * 
         * @param ServerRequestInterface $request The request object.
         * @param ResponseInterface $response The response object.
         * @param callable $next The next middleware to be called.
         * 
         * @return NULL|\Psr\Http\Message\ResponseInterface
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
        {
            $this->request = $request;
            $this->response = $response;

            $method = strtoupper($this->request->getMethod());
            switch ($method) {
                case "GET":
                    $this->get(Json::decode($this->getParameter("data")));
                    break;
                case "POST":
                    $this->create(Json::decode($this->getParameter("data")));
                    break;
                case "PATCH":
                    $this->update(Json::decode($this->getParameter("data")));
                    break;
                case "PUT":
                    $this->replace(Json::decode($this->getParameter("data")));
                    break;
                case "DELETE":
                    $this->delete(Json::decode($this->getParameter("data")));
                    break;
                case "OPTIONS":
                    $this->options();
                    break;
                case "HEAD":
                    $this->head(Json::decode($this->getParameter("data")));
                    $this->response = $this->response->withBody(new Stream(""));
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
         * Fetches the named parameter from the route matching first, and if nothing is found in the route, from
         * the parsed body if method type is POST, else from the query string. Returns NULL if nothing is found.
         * 
         * @param string $parameterHandle The handle of the parameter to be fetched.
         * @param mixed $default The default value to supply if no parameter value found.
         * 
         * @return string The parameter fetched.
         */
        protected function getParameter($parameterHandle, $default = NULL) {
            $param = NULL;

            $params = $this->request->getQueryParams();
            if (isset($params["$parameterHandle"])) {
                $param = $params["$parameterHandle"];
            }

            $parsedBody = $this->request->getParsedBody();
            if ($parsedBody && isset($parsedBody["$parameterHandle"])) {
                $param = $parsedBody["$parameterHandle"];
            }
            
            $result = $this->request->getAttribute($parameterHandle, $param);

            return is_null($result) ? $default : $result;
        }
    }
