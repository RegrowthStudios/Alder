<?php

    namespace Alder\PublicAuthentication\Middleware;
    
    use Alder\Container;
    use Alder\Token\Parser;
    use Alder\Token\Token;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The session middleware for Alder's public authentication service.
     * Determines if a visitor already has a session JWT and if so packs the data into 
     * the request message.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class SessionMiddleware implements MiddlewareInterface
    {
        /**
         * Process session data held by the user.
         * 
         * @param \Psr\Http\Message\ServerRequestInterface $request
         * @param \Psr\Http\Message\ResponseInterface $response
         * @param callable $next
         *
         * @return \Psr\Http\Message\ResponseInterface The resulting response of the middleware execution.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
        {
            $cookieParams = $request->getCookieParams();
            $sessionTokenString = $this->getParameter(USER_SESSION, isset($cookieParams[USER_SESSION]) ? $cookieParams[USER_SESSION] : NULL);
            
            if (is_null($sessionTokenString)) {
                return $next($request, $response);
            }
            
            $sessionToken = (new Parser())->parse($sessionTokenString);
            
            $result = $sessionToken->validate([
                "validators" => [
                    "sub" => "user"
                ]
            ]);
            
            if ($result !== Token::VALID) {
                $request = $request->withAttribute("visitor", [
                    "isLoggedIn" => false
                ]);
            } else {
                $request = $request->withAttribute("visitor", array_merge([
                    "isLoggedIn" => true
                ], $sessionToken->getClaims()[Container::get()->get("config")["alder"]["domain"]]));
            }
            
            return $next($request, $response);
        }
    }
