<?php

    namespace Alder\PublicAuthentication\Middleware;
    
    use Alder\Container;
    use Alder\Error\Stack as ErrorStack;
    use Alder\Middleware\MiddlewareTrait;
    use Alder\PublicAuthentication\User\SessionFactory;
    use Alder\Token\Parser;
    use Alder\Token\Token;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Json\Json;
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
        use MiddlewareTrait;

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
            $this->request = $request;
            $this->response = $response;

            $cookieParams = $this->request->getCookieParams();
            $sessionTokenString = $this->getParameter(USER_SESSION, isset($cookieParams[USER_SESSION]) ? $cookieParams[USER_SESSION] : NULL);
            
            if (!$sessionTokenString) {
                $this->request = $this->request->withAttribute("visitor", [
                    "isLoggedIn" => false
                ]);
                
                return $next($this->request, $this->response);
            }
            
            $sessionToken = (new Parser())->parse($sessionTokenString);
            
            $time = time();
            $result = $sessionToken->validate([
                "validators" => [
                    "sub" => "user"
                ]
            ]);

            if ($result !== Token::VALID) {
                // TODO(Matthew): Consider the following:
                // Not valid, treat as just not logged in or forbid access?
                // For now treating as not logged in - can just remove invalid tokens for second request after all.
                // Maybe log details for suspicious behaviour metric.
                $this->request = $this->request->withAttribute("visitor", [
                    "isLoggedIn" => false
                ]);
            } else {
                $config = Container::get()->get("config")["alder"];
                
                // Get application-specific claims of current token.
                $appClaims = Json::decode($sessionToken->getClaim($config["domain"]), Json::TYPE_ARRAY);
                  
                // If token is nearly expired, renew it.
                if ($sessionToken->getClaim("exp") - $time <= $config["security"]["refresh_sessions_with_expiry_within"]) {
                    $errors = new ErrorStack();
                    
                    // Generate new token.
                    $newCookie = SessionFactory::create($appClaims["id"], $errors, $appClaims, $appClaims["extended_session"]);
                    
                    if ($errors->notEmpty()) {
                        // Warn internally if new token could not be generated, but proceed - user may log in again if current token expires.
                        trigger_error("Failed to create replacement token for existing session.", E_USER_WARNING);
                    } else {
                        $this->response = $this->response->withAddedHeader("Set-Cookie", $newCookie);
                    }
                }
                
                $this->request = $this->request->withAttribute("visitor", array_merge([
                    "isLoggedIn" => true
                ], $appClaims));
            }
            
            return $next($this->request, $this->response);
        }
    }
