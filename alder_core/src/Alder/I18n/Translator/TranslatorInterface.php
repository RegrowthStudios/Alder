<?php
    
    namespace Alder\I18n\Translator;
    
    use Alder\I18n\Translator\Loader\LoaderInterface;
    
    /**
     * Provides an interface for translator classes.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    interface TranslatorInterface
    {
        /**
         * @const DEFAULT_DOMAIN Name of the default domain for messages.
         */
        public const DEFAULT_DOMAIN = "default";
        
        /**
         * Returns the current default locale of the translator.
         *
         * @return string
         */
        public function getDefaultLocale() : string;
        
        /**
         * Set the default locale of the translator.
         *
         * @param string $locale
         *
         * @return \Alder\I18n\Translator\TranslatorInterface
         */
        public function setDefaultLocale(string $locale) : TranslatorInterface;
        
        /**
         * Returns the current fallback locale of the translator.
         *
         * @return string
         */
        public function getFallbackLocale() : ?string;
        
        /**
         * Set the fallback locale of the translator. Used if the specified or default
         * locales yielded no message.
         *
         * @param string $locale
         *
         * @return \Alder\I18n\Translator\TranslatorInterface
         */
        public function setFallbackLocale(string $locale) : TranslatorInterface;
        
        /**
         * Set the loader from which to load messages for translations.
         *
         * @param \Alder\I18n\Translator\Loader\LoaderInterface $loader
         *
         * @return \Alder\I18n\Translator\TranslatorInterface
         */
        public function setLoader(LoaderInterface $loader) : TranslatorInterface;
        
        /**
         * Gets the message with the specified label in the specified domain. The locale
         * of the retrieved message is the locale specified if it is not null, otherwise
         * the default for the translator instance.
         *
         * @param string      $label  The message to be retrieved's label.
         * @param string      $domain The domain in which the message to be retrieved resides.
         * @param string|null $locale The locale in which the message should be returned.
         *
         * @return string|null The message retrieved or null if nothing found.
         */
        public function translate(string $label, string $domain = self::DEFAULT_DOMAIN,
                                  string $locale = null) : ?string;
        
        /**
         * Similar to translate, but handles differentiating between singular and plural phrasing.
         * This is achieved by allowing an array of plural labels, where the numeric index of each
         * entry in the array indicates the value of $count to start using the phrase specified by
         * the associated label.
         *
         * E.g. for $singularLabel === "singular" and $pluralLabels === [ 2 => "plural1", 4 => "plural2" ]:
         *        if $count === 1, message with label "singular" is returned;
         *        if $count === 2 or 3, message with label "plural1" is returned;
         *        if $count  >= 4, message with label "plural2" is returned.
         *
         * @param string       $singularLabel The label of the message for the singular phrasing.
         * @param array|string $pluralLabels  The label(s) of the message for different plural phrasings.
         * @param int          $count         The count of the item indicating plurality of the phrasing.
         * @param string       $domain        The domain in which the message to be retrieved resides.
         * @param string|null  $locale        The locale in which the message should be returned.
         *
         * @return null|string The message retrieved or null if nothing found.
         */
        public function translatePlural(string $singularLabel, $pluralLabels, int $count,
                                        string $domain = self::DEFAULT_DOMAIN, string $locale = null) : ?string;
    }
