<?php
    
    namespace Alder\PublicAuthentication\Middleware;
    
    use Alder\DiContainer;
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
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class SessionMiddleware implements MiddlewareInterface
    {
        use MiddlewareTrait;
        
        /**
         * Process session data held by the user.
         *
         * @param \Psr\Http\Message\ServerRequestInterface $request
         * @param \Psr\Http\Message\ResponseInterface      $response
         * @param callable                                 $next
         *
         * @return \Psr\Http\Message\ResponseInterface The resulting response of the middleware execution.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            $this->request = $request;
            $this->response = $response;
    
            $visitorDetails = $this->fetchClientSideSessionInfo();
    
            $visitorDetails = array_merge(
                $visitorDetails,
                $this->fetchServerSideSessionInfo($visitorDetails)
            );
                
            $this->request = $this->request->withAttribute("visitor", $visitorDetails);
            
            return $next($this->request, $this->response);
        }
    
        /**
         * Fetches client-side session info stored in the user session JWT.
         *
         * @return array
         */
        protected function fetchClientSideSessionInfo() : array {
            // Get cookies from client.
            $cookieParams = $this->request->getCookieParams();
            // Get session token string first from parameters of request, second from cookies.
            $sessionTokenString = $this->getParameter(USER_SESSION, $cookieParams[USER_SESSION] ?? null);
    
            // If the session token string is empty or null, set the visitory as not logged in.
            if (!$sessionTokenString) {
                return ["is_logged_in" => false];
            }
    
            // Construct a session token from the string obtained.
            $sessionToken = (new Parser())->parse($sessionTokenString);
    
            // Validate the session token.
            $result = $sessionToken->validate(["validators" => ["sub" => "user"]]);
    
            // If the token is not valid, set the visitor as not logged in.
            if ($result !== Token::VALID) {
                // TODO(Matthew): Consider the following:
                // Not valid, treat as just not logged in or forbid access?
                // For now treating as not logged in - can just remove invalid tokens for second request after all.
                // Maybe log details for suspicious behaviour metric.
                return ["is_logged_in" => false];
            } else {
                // Fetch app config.
                $config = DiContainer::get()->get("config")["alder"];
        
                // Get app-specific claims of current token.
                $appClaims = Json::decode($sessionToken->getClaim($config["domain"]), Json::TYPE_ARRAY);
    
                // If token is nearly expired, renew it.
                if ($sessionToken->getClaim("exp") - time()
                    <= $config["security"]["refresh_sessions_with_expiry_within"]
                ) {
                    $this->regenerateSessionToken($appClaims);
                }
        
                return array_merge(["is_logged_in" => true], $appClaims);
            }
        }
    
        /**
         * Regenerates the session token of the visitor.
         *
         * @param array $appClaims
         */
        protected function regenerateSessionToken(array $appClaims) : void {
            $errors = new ErrorStack();
    
            // Generate new token.
            $newCookie = SessionFactory::create($appClaims["id"], $errors, $appClaims,
                                                $appClaims["extended_session"]);
    
            if ($errors->notEmpty()) {
                // Warn internally if new token could not be generated, but proceed - user may log in again if current token expires.
                trigger_error("Failed to create replacement token for existing session.", E_USER_WARNING);
            } else {
                $this->response = $this->response->withAddedHeader("Set-Cookie", $newCookie);
            }
        }
        
        protected function fetchServerSideSessionInfo(array $visitorDetails) : array {
            $sessionCache = DiContainer::get()->get("AlderSessionCache");
            
            if (!$visitorDetails["is_logged_in"]) {
                // TODO(Matthew): Think some more about this whole implementation. Would be nice to have a
                // unified method to get and set visitor information both server-side and client-side and
                // also "save" said data. (I.e. regenerate session token/update cache).
            }
        }
    }
