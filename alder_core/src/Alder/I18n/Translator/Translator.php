<?php
    
    namespace Alder\I18n\Translator;
    
    class Translator extends AbstractTranslator
    {
        /**
         * {@inheritdoc}
         */
        public function translate(string $label, string $domain = self::DEFAULT_DOMAIN,
                                  string $locale = null) : ?string {
            
        }
        
        /**
         * {@inheritdoc}
         */
        public function translatePlural(string $singularLabel, $pluralLabels, int $count,
                                        string $domain = self::DEFAULT_DOMAIN, string $locale = null) : ?string {
            
        }
    }
