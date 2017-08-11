<?php
    
    namespace Alder\Admin\Install\Middleware;
    
    use Alder\DiContainer;
    use Alder\ApiMap\Factory as ApiMapFactory;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Uri;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * Determines if installation is required, and if so redirects to /admin/install.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class InstallNeededMiddleware implements MiddlewareInterface
    {
        /**
         * Determine if installation is required and if so redirects to /admin/install.
         *
         * @param \Psr\Http\Message\ServerRequestInterface $request  The data of the request.
         * @param \Psr\Http\Message\ResponseInterface      $response The response to be sent back.
         * @param callable|NULL                            $next     The next middleware to be called.
         *
         * @return \Psr\Http\Message\ResponseInterface The response produced.
         */
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            // Figure  if installation is required.
            if (!DiContainer::get()->get("config")->alder->installed) {
                /*
                 * @var \Zend\Expressive\Helper\ServerUrlHelper $uri
                 */
                $uri = DiContainer::get()->get(\Zend\Expressive\Helper\ServerUrlHelper::class);

                // TODO(Matthew): Are we confident this gives the right URL for the request?
                $request = $request->withUri(Uri($uri("/admin/install")));
            }
            
            return $next($request, $response);
        }
    }
