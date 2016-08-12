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
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
        {
            $cookieParams = $request->getCookieParams();
            
            if (!isset($cookieParams["alis"]) || !($alis = $cookieParams["alis"])) {
                $next($request, $response);
            }
            
            $alisToken = (new Parser())->parse($alis);
            
            $result = $alisToken->validate([
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
                ], $alisToken->getClaims()[Container::get()->get("config")["alder"]["domain"]]));
            }
            
            $next($request, $response);
        }
    }
