<?php
    
    namespace Alder\Middleware;
    
    /**
     * Provides extra functionality for middleware.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    trait MiddlewareTrait
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
         * Determines if a parameter with the handle passed in was provided in the request.
         *
         * @param string $parameterHandle The handle of the parameter to be fetched.
         *
         * @return bool True if the parameter exists, false otherwise.
         */
        protected function hasParameter($parameterHandle) {
            $queryParams = $this->request->getQueryParams();
            $parsedBody = $this->request->getParsedBody();
            $attributes = $this->request->getAttributes();

            return isset($queryParams[$parameterHandle])
                || isset($parsedBody[$parameterHandle])
                || isset($attributes[$parameterHandle]);
        }

        /**
         * Fetches the named parameter from the route matching first, and if nothing is found in the route, from
         * the parsed body if method type is POST, else from the query string. Returns NULL if nothing is found.
         *
         * @param string $parameterHandle The handle of the parameter to be fetched.
         * @param mixed  $default         The default value to supply if no parameter value found.
         *
         * @return string The parameter fetched.
         */
        protected function getParameter(string $parameterHandle, $default = null) : string {
            $param = null;
            
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
        
        /**
         * Returns all parameters of the request.
         * 
         * @return array The parameters of the request.
         */
        protected function getParameters() {
            $queryParams = $this->request->getQueryParams();
            $parsedBody = (array) $this->request->getParsedBody();
            $attributes = $this->request->getAttributes();
            
            // TODO(Matthew): Consider alternative merging strategies.
            return array_merge($queryParams, $parsedBody, $attributes);
        }
    }
