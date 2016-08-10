<?php

    namespace Alder
    {
        use Interop\Container\ContainerInterface;
        
        /**
         * Simple class for acquiring this application instance's copy
         * of the dependency injector.
         * 
         * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
         * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
         * @since 0.1.0
         */
        class Container
        {
            /**
             * @var \Interop\Container\ContainerInterface
             */
            private static $container;
            
            /**
             * Set the container for this application instance.
             * 
             * @param \Interop\Container\ContainerInterface $container
             */
            public static function set(ContainerInterface $container) {
                self::$container = $container;
            }
            
            /**
             * Returns the container for this application instance.
             * 
             * @return \Interop\Container\ContainerInterface
             */
            public static function get() {
                return self::$container;
            }
        }
    }

    /**
     * Builds a file path from the given segments using DIRECTORY_SEPARATOR.
     * 
     * @param variadic $segments The segments to build the path from.
     */
    function file_build_path(...$segments)
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }