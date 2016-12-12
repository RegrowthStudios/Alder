<?php
    
    namespace Alder\I18n\Translator;
    
    use Alder\I18n\Translator\Loader\LoaderInterface;
    
    abstract class AbstractTranslator implements TranslatorInterface
    {
        /**
         * @var string $defaultLocale
         */
        protected $defaultLocale;
        
        /**
         * @var string|null $fallbackLocale
         */
        protected $fallbackLocale = null;
        
        /**
         * @var \Alder\I18n\Translator\Loader\LoaderInterface $loader
         */
        protected $loader;
        
        /**
         * {@inheritdoc}
         */
        public function getDefaultLocale() : string {
            return $this->defaultLocale;
        }
        
        /**
         * {@inheritdoc}
         */
        public function setDefaultLocale(string $locale) : TranslatorInterface {
            $this->defaultLocale = $locale;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function getFallbackLocale() : ?string {
            return $this->fallbackLocale;
        }
        
        /**
         * {@inheritdoc}
         */
        public function setFallbackLocale(string $locale) : TranslatorInterface {
            $this->fallbackLocale = $locale;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function getLoader() : LoaderInterface {
            return $this->loader;
        }
        
        /**
         * {@inheritdoc}
         */
        public function setLoader(LoaderInterface $loader) : TranslatorInterface {
            $this->loader = $loader;
            
            return $this;
        }
    }
