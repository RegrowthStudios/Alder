<?php
    /**
     * Created by PhpStorm.
     * User: matthew
     * Date: 22/11/16
     * Time: 18:09
     */
    
    namespace Alder;

    use Interop\Container\ContainerInterface;
    
    /**
     * Simple class for acquiring this application instance's copy
     * of the dependency injector.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class DiContainer
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