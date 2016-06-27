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
                "application/json"
            ]
        ];
        
        /**
         * Determines the appropriate action function to call for the request method and parameters.
         * 
         * @param ServerRequestInterface $request The request object.
         * @param ResponseInterface $response The response object.
         * 
         * @param callable $next The next middleware to be called.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \callable $next = null)
        {
            $this->request = $request;
            $this->response = $response;
            
            $method = strtoupper($request->getMethod());
            switch($method) {
                case "DELETE":
                    
            }
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
         * @param ServerRequestInterface $request The request object.
         * 
         * @return object|string|array
         */
        protected function processBodyContent(ServerRequestInterface $request)
        {
            // TODO(Matthew): This is a stream, and body could be HUGE, so we should be checking size and rejecting bodies of ridiculous size.
            //                Should be done at earlier stage, e.g. at pre-routing stage.
            $body = (string) $request->getBody();
            
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
