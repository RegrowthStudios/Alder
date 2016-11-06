<?php
    
    namespace Alder\PublicAuthentication\Middleware;
    
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
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null) {
            // TODO(Matthew): Figure how Expressive does DI so as to obtain the configured default locale.
            $locale = $request->getAttribute("locale", "en_GB");
            // TODO(Matthew): Do language middleware separate or here?
            \Locale::setDefault($locale);
            
            return $next($request, $response);
        }
    }
