<?php
    
    namespace Alder\I18n\Translator;
    
    use Alder\I18n\Translator\Loader\Loader;
    
    class TranslatorFactory
    {
        public static function create(string $defaultLocale, string $defaultBaseDirectory,
                                      string $fallbackLocale = null, array $files = [], array $filePatterns = [],
                                      $loaderCache = null) {
            if (!$loaderCache) {
                // TODO(Matthew): Prepare default cache object.
            }
            
            $loader = (new Loader())->setCache($loaderCache)
                                    ->setDefaultBaseDirectory($defaultBaseDirectory);
            
            $expectedFileParts = [
                "filename",
                "locale"
            ];
            foreach ($files as $file) {
                if (!is_array($file) || !array_key_exists($file, $expectedFileParts)) {
                    continue;
                }
                $loader->addTranslationFile(
                    $file["filename"],
                    $file["locale"],
                    ($file["domain"] ?? Translator::DEFAULT_DOMAIN),
                    ($file["baseDirectory"] ?? null)
                );
            }
            foreach ($filePatterns as $filePattern) {
                if (!is_array($filePattern) || !isset($filePattern["pattern"])) {
                    continue;
                }
                $loader->addTranslationFilePattern(
                    $filePattern["pattern"],
                    ($filePattern["domain"] ?? Translator::DEFAULT_DOMAIN),
                    ($filePattern["baseDirectory"] ?? null)
                );
            }
            
            return (new Translator())->setDefaultLocale($defaultLocale)
                                     ->setFallbackLocale($fallbackLocale)
                                     ->setLoader($loader);
        }
    }
