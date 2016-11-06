<?php
    
    namespace Alder\PublicAuthentication\Middleware;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Response\JsonResponse;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The API map middleware for Alder's public authentication service.
     * Provides the API map of the service on request.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ApiMapMiddleware implements MiddlewareInterface
    {
        /**
         * Sets response to be the API map of the service if path is to root/asterisk
         * and request method is OPTIONS.
         *
         * @param \Psr\Http\Message\ServerRequestInterface $request  The data of the request.
         * @param \Psr\Http\Message\ResponseInterface      $response The response to be sent back.
         * @param callable|NULL                            $next     The next middleware to be called.
         *
         * @return \Psr\Http\Message\ResponseInterface The response produced.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null) : ResponseInterface {
            // If request method is OPTIONS and the path is empty, then treat request as a ping.
            $path = $request->getUri()->getPath();
            if ($request->getMethod() === "OPTIONS"
                && ($path === "" || $path === "/" || $path === "*")
            ) {
                return new JsonResponse(require file_build_path(APP_DIRECTORY, "api-map.php"));
            }
            
            return $next($request, $response);
        }
    }
