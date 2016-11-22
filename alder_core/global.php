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

    namespace {
        /**
         * Builds a file path from the given segments using DIRECTORY_SEPARATOR.
         *
         * @param array ...$segments The segments to build the path from.
         *
         * @return string The resulting file path.
         */
        function file_build_path(...$segments)
        {
            return join(DIRECTORY_SEPARATOR, $segments);
        }
        
        /**
         * Builds a cookie string using the provided parameters.
         * 
         * @param string $name The name of the cookie.
         * @param string $value The value of the cookie.
         * @param integer|string $expiryTime The expiry time in seconds since the epoch.
         * @param string $domain The domain of the cookie.
         * @param string $path The path of the cookie.
         * @param boolean $secure Whether the cookie can only be sent over encrypted messages.
         * @param boolean $httpOnly Whether the cookie can only be sent over the HTTP protocol.
         * 
         * @return string The built cookie.
         */
        function build_cookie(string $name, string $value, $expiryTime, string $domain, string $path = "/", bool $secure = true, bool $httpOnly = true)
        {
            if (!is_numeric($expiryTime)) {
                throw new \InvalidArgumentException("Parameter 'expiryTime' must be numeric.");
            }
            
            $cookie = "$name=$value; Expires=" . gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $expiryTime) . "; Domain=$domain; Path=$path";
            if ($secure !== false) {
                $cookie .= "; Secure";
            }
            if ($httpOnly !== false) {
                $cookie .= "; HttpOnly";
            }
            
            return $cookie;
        }
        
        // TODO(Matthew): Use this instead of arbitrary strings for constants, therefore can reduce heaviness of this function.
        /**
         * Canonicalises a given action class path.
         *
         * @param string $classpath
         *
         * @return mixed
         */
        function canonicalise_action_class_path(string $classpath)
        {
            $classpathParts = explode("\\", get_class($this));
            $classname = end($classpathParts);
            
            $resourceName = str_replace("Action", "", $classname);
            
            $separatedByUnderscores = preg_replace("/\B([A-Z])/", "_$1", $resourceName);
            $uppercased = strtoupper($separatedByUnderscores);
            
            return constant($uppercased);
        }
    }
