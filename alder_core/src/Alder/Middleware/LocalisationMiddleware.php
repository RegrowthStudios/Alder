<?php
    
    namespace Alder\Middleware;
    
    use Alder\Container;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The localisation middleware for Alder's public authentication service.
     * Determines the localisation of the request, if none or an invalid locale
     * is provided, then the configured default locale is used.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class LocalisationMiddleware implements MiddlewareInterface
    {
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            $config = Container::get()->get("config");
            
            $locale = $request->getAttribute("locale", $config["default_locale"]);
            
            // TODO(Matthew): Do language middleware separate or here?
            \Locale::setDefault($locale);
            
            return $next($request, $response);
        }
    }
