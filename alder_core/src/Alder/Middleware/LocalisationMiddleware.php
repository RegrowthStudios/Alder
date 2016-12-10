<?php
    
    namespace Alder\Middleware;
    
    use Alder\DiContainer;
    
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
            $config = DiContainer::get()->get("config");
            
            $locale = $request->getAttribute("locale", ($config["default_locale"] ?: \Locale::getDefault()));
            
            $translator = Translator::factory([
                "locale" => $locale,
                "translation_file_patterns" => [
                    [
                        "type" => PhpArray::class,
                        "base_dir" => LANGUAGE_DIRECTORY,
                        "pattern" => file_build_path("%s", "")
                    ]
                ]
            ]);
            
            
            return $next($request, $response);
        }
    }
