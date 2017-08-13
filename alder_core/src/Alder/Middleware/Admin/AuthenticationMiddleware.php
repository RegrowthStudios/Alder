<?php
    
    namespace Alder\Middleware\Admin;
    
    use Alder\DiContainer;
    use Alder\Middleware\MiddlewareTrait;
    use Alder\Token\Validator;
    
    use Lcobucci\JWT\Parser;

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Uri;
    use Zend\Diactoros\Response\EmptyResponse;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * Determines if requestee is authenticated sufficiently to access admin section of application.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class InstallNeededMiddleware implements MiddlewareInterface
    {
        use MiddlewareTrait;

        /**
         * Determine if requestee is authenticated sufficiently to access admin section of application.
         *
         * @param callable|NULL $next The next middleware to be called.
         *
         * @return \Psr\Http\Message\ResponseInterface The response produced.
         */
        public function call(callable $next = null) : ResponseInterface {
            $config = DiContainer::get()->get("config")->alder;
            $installed      = $config->installed;
            $adminRouteRoot = $config->admin->route_root;

            if (!$installed || $this->request->getUri()->getPath() == "/$adminRouteRoot/login") {
                return $next($this->request, $this->response);
            }

            // Get cookies.
            $cookies = $this->request->getCookieParams();
            // Get most authoratative session token.
            $sessionTokenString = $this->getParameter(ADMIN_SESSION, $cookies[ADMIN_SESSION] ?? null);

            if (!$sessionTokenString) {
                if (strtoupper($this->request->getMethod()) == "GET") {
                    // TODO(Matthew): Should we instead show a login view with a "danger" dialog but without redirecting?
                    /*
                     * @var \Zend\Expressive\Helper\ServerUrlHelper $uri
                     */
                    $uri = DiContainer::get()->get(\Zend\Expressive\Helper\ServerUrlHelper::class);
    
                    $adminRouteRoot = DiContainer::get()->get("config")->alder->admin->route_root;
                    
                    header("Location: " . $uri("/$adminRouteRoot/login"), true, 303);
                    exit();
                } else {
                    return new EmptyResponse(403);
                }
            }

            // TODO(Matthew): Create admin session token generator/validator class.
            $sessionToken = (new Parser())->parse($sessionTokenString);

            $result = Validator::validate($sessionToken, [
                "public" => [
                    "sub" => "admin"
                ]
            ]);

            // TODO(Matthew): Result fail may return a string rather than false.
            //                (Validator is incomplete.)
            if (!$result) {
                if (strtoupper($this->request->getMethod()) == "GET") {
                    // TODO(Matthew): Should we instead show a login view with a "danger" dialog but without redirecting?
                    /*
                     * @var \Zend\Expressive\Helper\ServerUrlHelper $uri
                     */
                    $uri = DiContainer::get()->get(\Zend\Expressive\Helper\ServerUrlHelper::class);
    
                    $adminRouteRoot = DiContainer::get()->get("config")->alder->admin->route_root;

                    header("Location: " . $uri("/$adminRouteRoot/login"), true, 303);
                    exit();
                } else {
                    return new EmptyResponse(403);
                }
            }

            return $next($this->request, $this->response);
        }
    }
