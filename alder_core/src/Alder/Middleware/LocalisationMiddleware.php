<?php
    
    namespace Alder\Middleware;
    
    use Alder\DiContainer;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\I18n\Translator\Translator;
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
            /**
             * @var \Zend\ServiceManager\ServiceManager $container
             */
            $container = DiContainer::get();
            $config = $container->get("config")["alder"]["language"];
            
            $locale = $request->getAttribute("locale", ($config["default_locale"] ?: \Locale::getDefault()));
            
            $languageSources = $config["language_sources"];
            
            $translator = Translator::factory([
                "locale" => $locale,
                "translation_file_patterns" => $languageSources["file_patterns"],
                "translation_files" => $languageSources["files"],
                "remote_translation" => $languageSources["remote_files"],
                "cache" => $container->get("alder_language_cache")
            ]);
            
            if ($config["fallback_locale"]) {
                $translator->setFallbackLocale($config["fallback_locale"]);
            }
            
            $container->setService("translator", $translator);
            
            return $next($request, $response);
        }
    }
