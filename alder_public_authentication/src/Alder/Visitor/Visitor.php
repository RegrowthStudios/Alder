<?php
    
    namespace Alder\Visitor;
    
    use Alder\Container;
    use Alder\Error\Stack as ErrorStack;
    use Alder\Middleware\MiddlewareTrait;
    use Alder\PublicAuthentication\User\SessionFactory;
    use Alder\Visitor\VisitorInfoPacket\VisitorInfoPacket;
    use Alder\Visitor\VisitorInfoPacket\VisitorInfoPacketInterface;
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
        
        public const COOKIE = "cookie";
        
        public const SERVER_CACHE = "server_cache";
        
        public function __construct(ServerRequestInterface $request, ResponseInterface $response) {
            $this->request = $request;
            $this->response = $response;
            
            // Acquire visitor session data from configured sources.
            $sources = Container::get()->get("config")["session_sources"];
            foreach ($sources as $name => $source) {
                if (!isset($source["type"])) {
                    continue;
                }
                // Determine if a particular visitor info packet class should be used.
                if (class_exists($classpath = ($source["info_packet_classpath"] ?? ""))) {
                    $infoPacket = new $classpath();
                } else {
                    $infoPacket = new VisitorInfoPacket();
                }
                // Call the appropriate acquisition procedure.
                if ($source["type"] === self::COOKIE) {
                    $this->acquireCookie(
                        $name,
                        $infoPacket,
                        $source["validators"] ?? [],
                        $source["noTokenCallback"] ?? null,
                        $source["invalidTokenCallback"] ?? null,
                        $source["namespaces"] ?? ["alder"],
                        $source["refresh"] ?? true,
                        $source["when"] ?? null
                    );
                } else {
                    if ($source["type"] === self::SERVER_CACHE) {
                        $this->acquireServerSideCache(
                            $name,
                            $infoPacket/*$source["validators"] ?? [],
                        $source["noTokenCallback"] ?? null,
                        $source["invalidTokenCallback"] ?? null,
                        $source["namespaces"] ?? ["alder"],
                        $source["refresh"] ?? true,
                        $source["when"] ?? null*/
                        );
                    }
                }
            }
        }
        
        protected $data = [];
        
        public function fetch(string $key) : ?VisitorInfoPacketInterface {
            return $this->data[$key] ?? null;
        }
        
        public function saveModified() : bool {
            foreach ($this->data as $datum) {
                if ($datum->hasChanged()) {
                    $datum->save();
                }
            }
        }
        
        protected function acquireCookie(string $key, VisitorInfoPacketInterface $cookie, array $validators,
                                         callable $noTokenCallback, callable $invalidTokenCallback, array $namespaces,
                                         bool $refresh, int $when) : void {
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
                    $appClaims[$namespace] = array_merge(
                        $appClaims,
                        Json::decode($token->getClaim($namespace), Json::TYPE_ARRAY)
                    );
                }
                
                // If token is nearly expired, renew it.
                $defaultWhen = Container::get()->get(
                        "config"
                    )["alder"]["security"]["refresh_sessions_with_expiry_within"];
                if ($refresh && $token->getClaim("exp") - time() <= $when ?? $defaultWhen) {
                    $this->regenerateToken($appClaims);
                }
                
                // Return and cache the app-specific claims of the token.
                $this->data[$key] = $cookie->initialise($appClaims);
            }
        }
        
        protected function acquireServerSideCache(string $key, VisitorInfoPacketInterface $cache) {
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
            $newCookie = SessionFactory::create(
                $appClaims["id"],
                $errors,
                $appClaims,
                $appClaims["extended_session"] ?? false
            );
            
            if ($errors->notEmpty()) {
                // Warn internally if new token could not be generated, but proceed - user may log in again if current token expires.
                trigger_error("Failed to create replacement token for existing session.", E_USER_WARNING);
            } else {
                $this->response = $this->response->withAddedHeader("Set-VisitorInfoPacket", $newCookie);
            }
        }
    }
