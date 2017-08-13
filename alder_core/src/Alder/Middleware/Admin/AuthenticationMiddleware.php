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
            if (!DiContainer::get()->get("config")->alder->installed ||
                $request->getUri()->getPath() == "/admin/login") {
                return $next($request, $response);
            }

            // Get cookies.
            $cookies = $request->getCookieParams();
            // Get most authoratative session token.
            $sessionTokenString = $this->getParameter(ADMIN_SESSION, $cookies[ADMIN_SESSION] ?? null);

            if (!$sessionTokenString) {
                // TODO(Matthew): Handle this by redirecting to /admin/install on GET and 403 on other method if admin is not obfuscated.
                //                If admin is obfuscated, show 404.
                return new EmptyResponse(403);
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
                return new EmptyResponse(403);
            }

            return $next($request, $response);
        }
    }
