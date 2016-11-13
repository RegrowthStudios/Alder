<?php
    
    namespace Alder\PublicAuthentication\Visitor;
    
    use Alder\Container;
    use Alder\Error\Stack as ErrorStack;
    use Alder\Middleware\MiddlewareTrait;
    use Alder\PublicAuthentication\User\SessionFactory;
    use Alder\PublicAuthentication\Visitor\Cookie\Cookie;
    use Alder\PublicAuthentication\Visitor\Cookie\CookieInterface;
    use Alder\Token\Parser;
    use Alder\Token\Token;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Json\Json;
    
    /**
     * Provides functionality for getting and updating visitor session information both client-side and server-side.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Visitor
    {
        use MiddlewareTrait;
        
        public function __construct(ServerRequestInterface $request, ResponseInterface $response) {
            $this->request = $request;
            $this->response = $response;
            
            $sources = Container::get()->get("config")["session_sources"];
            foreach ($sources as $name => $source) {
                
            }
            
            // By default, fetch user session cookie.
            //$this->fetchCookie(USER_SESSION);
        }
        
        protected $data = [];
        
        protected function acquireCookie(string $key, CookieInterface& $cookie, array $validators = [],
                                    callable $noTokenCallback = null, callable $invalidTokenCallback = null, array $namespaces = ["alder"], bool $refresh = true,
                                    int $when = null) : void {
            if (isset($this->data[$key])) {
                $cookie = $this->data[$key];
                return;
            }
            
            // Get cookies from client.
            $cookieParams = $this->request->getCookieParams();
            // Get token string first from parameters of request, second from cookies.
            $tokenString = $this->getParameter($key, $cookieParams[$key] ?? null);
            
            // If the session token string is empty or null, set the visitory as not logged in.
            if (!$tokenString) {
                $this->data[$key] = $cookie->initialise();
                if ($noTokenCallback) {
                    $noTokenCallback();
                }
                return;
            }
            
            // Construct a token from the token string obtained.
            $token = (new Parser())->parse($tokenString);
            
            // Validate the session token.
            $result = $token->validate(["validators" => $validators]);
            
            // If the token is not valid, set the visitor as not logged in.
            if ($result !== Token::VALID) {
                // TODO(Matthew): Consider the following:
                // Not valid, treat as just not logged in or forbid access?
                // For now treating as not logged in - can just remove invalid tokens for second request after all.
                // Maybe log details for suspicious behaviour metric.
                $this->data[$key] = $cookie->initialise();
                if ($invalidTokenCallback) {
                    $invalidTokenCallback();
                }
                return;
            } else {
                // Get app-specific claims of current token.
                $appClaims = [];
                foreach ($namespaces as $namespace) {
                    $appClaims[$namespace] = array_merge($appClaims,
                                                         Json::decode($token->getClaim($namespace), Json::TYPE_ARRAY));
                }
                
                // If token is nearly expired, renew it.
                $defaultWhen = Container::get()
                                        ->get("config")["alder"]["security"]["refresh_sessions_with_expiry_within"];
                if ($refresh && $token->getClaim("exp") - time() <= $when ?? $defaultWhen) {
                    $this->regenerateToken($appClaims);
                }
                
                // TODO(Matthew): Should we be also returning registered claims?
                // Return and cache the app-specific claims of the token.
                $this->data[$key] = $cookie->initialise($appClaims);
            }
        }
        
        protected function acquireServerSideInfo(string $key) {
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
        
        public function saveModified() : bool {
            foreach($this->data as $datum) {
                if ($datum->hasChanged()) {
                    $datum->save();
                }
            }
        }
        
        // TODO(Matthew): This makes no sense if we're allowing multiple cookies that might need regenerating.
        //                Currently assumes we're only regenerating the core session token... we're not.
        /**
         * Regenerates the session token of the visitor.
         *
         * @param array $appClaims
         */
        protected function regenerateToken(array $appClaims) : void {
            $errors = new ErrorStack();
            
            // Generate new token.
            $newCookie = SessionFactory::create($appClaims["id"], $errors, $appClaims,
                                                $appClaims["extended_session"] ?? false);
            
            if ($errors->notEmpty()) {
                // Warn internally if new token could not be generated, but proceed - user may log in again if current token expires.
                trigger_error("Failed to create replacement token for existing session.", E_USER_WARNING);
            } else {
                $this->response = $this->response->withAddedHeader("Set-Cookie", $newCookie);
            }
        }
    }
