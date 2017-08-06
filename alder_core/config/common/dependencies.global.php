<?php

    /**
     * Global dependencies required by the entire application.
     * 
     * TODO(Matthew): Determine which of the current dependencies are actually needed.
     */

    return [
        "dependencies" => [
            "invokables" => [
                \Zend\Expressive\Helper\ServerUrlHelper::class => Zend\Expressive\Helper\ServerUrlHelper::class,
                \Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class
            ],
            "factories" => [
                \Zend\Expressive\Application::class => Zend\Expressive\Container\ApplicationFactory::class,
                \Zend\Expressive\Helper\UrlHelper::class => Zend\Expressive\Helper\UrlHelperFactory::class,
                \Zend\Db\Adapter\Adapter::class => function (\Interop\Container\ContainerInterface $container) {
                    $config = $container->get("config")["alder"];
                    return new \Zend\Db\Adapter\Adapter($config["db"]["adapter"]);
                },
                \Zend\Db\Metadata\MetadataInterface::class => function (\Interop\Container\ContainerInterface $container) {
                    $adapter = $container->get(\Zend\Db\Adapter\Adapter::class);
                    return \Zend\Db\Metadata\Source\Factory::createSourceFromAdapter($adapter);
                },
                "alder_db_cache" => function (\Interop\Container\ContainerInterface $container) {
                    return \Alder\Cache\CacheServiceFactory::create("db");
                },
                "translator" => function (\Interop\Container\ContainerInterface $container) {
                    $config = $container->get("config")["alder"]["language"];
    
                    $locale = ($config["default_locale"] ?: \Locale::getDefault());
    
                    $languageSources = $config["language_sources"];
    
                    $translator = \Zend\I18n\Translator\Translator::factory([
                        "locale" => $locale,
                        "translation_file_patterns" => $languageSources["file_patterns"],
                        "translation_files" => $languageSources["files"],
                        "remote_translation" => $languageSources["remote_files"],
                        "cache" => \Alder\Cache\CacheServiceFactory::create("language")
                    ]);
    
                    if ($config["fallback_locale"]) {
                        $translator->setFallbackLocale($config["fallback_locale"]);
                    }
                },
                "token_signer" => function(\Interop\Container\ContainerInterface $container) {
                    $signMethod = $container->get("config")["alder"]["token"]["sign_method"];
                    return new $signMethod();
                }
            ],
        ],
    ];
