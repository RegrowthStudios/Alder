<?php

    namespace Alder\PublicAuthentication\Middleware;

    use Alder\Error\Error;
    use Alder\Middleware\MiddlewareTrait;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;

    use Zend\Diactoros\Response\JsonResponse;
    use Zend\Json\Json;
    use Zend\Stratigility\MiddlewareInterface;

    /**
     * Provides a parser for parsing JSON encoded request bodies and parameters.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class RequestParserMiddleware implements MiddlewareInterface
    {
        use MiddlewareTrait;
        
        // TODO(Matthew): Accept YAML in requests.
        // TODO(Matthew): Is decoding of parameters done well?
        /**
         * If the content type header is "application/json", parses the raw body stream as a JSON string
         * and sets the request object's parsedBody array as the result. Request parameters are then checked 
         * for JSON encoding and decoded as appropriate.
         *
         * @param \Psr\Http\Message\ServerRequestInterface $request The data of the request.
         * @param \Psr\Http\Message\ResponseInterface $response The response to be sent back.
         * @param callable|NULL $next The next middleware to be called.
         *
         * @return \Psr\Http\Message\ResponseInterface The response produced.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = NULL) {
            $this->request = $request;
            $this->response = $response;
            
            $contentType = $this->request->getHeader("Content-Type");
            if (strpos($contentType, "application/json") === 0) {
                try {
                    $parsedBody = Json::decode($this->request->getBody()->getContents(), Json::TYPE_ARRAY);
                } catch (\RuntimeException $ex) {
                    return new JsonResponse([
                        "errors" => [
                            102010101 => Error::retrieveString(102010101)
                        ]
                    ], 400);
                }
                $this->request = $this->request->withParsedBody($parsedBody);
            }
            
            // Decode parameters in case more than the body was JSON.
            foreach ($this->getParameters() as $key => $parameter) {
                if (is_string($parameter)) {
                    try {
                        $parameter = Json::decode($parameter, Json::TYPE_ARRAY);
                    } catch (\RuntimeException $ex) {
                        continue;
                    }
                    $this->request = $this->request->withAttribute($key, $parameter);
                }
            }
            
            return $next($this->request, $this->response);
        }
    }
