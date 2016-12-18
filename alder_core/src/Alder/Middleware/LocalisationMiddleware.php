<?php
    
    namespace Alder\Middleware;
    
    use Alder\DiContainer;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\I18n\Translator\Translator;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The localisation middleware for providing a translation service to the application.
     * Determines the localisation of the request, if none or an invalid locale
     * is provided, then the configured default locale is used. A fallback locale may also
     * be configured.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class LocalisationMiddleware implements MiddlewareInterface
    {
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            /**
             * @var \Zend\I18n\Translator\Translator $translator
             */
            $translator = DiContainer::get()->get("translator");
            $translator->setLocale($request->getAttribute("locale", $translator->getLocale()));
            
            return $next($request, $response);
        }
    }
