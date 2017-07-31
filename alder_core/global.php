<?php
    
    /**
     * Builds a file path from the given segments using DIRECTORY_SEPARATOR.
     *
     * @param array ...$segments The segments to build the path from.
     *
     * @return string The resulting file path.
     */
    function file_build_path(...$segments) {
        return join(DIRECTORY_SEPARATOR, $segments);
    }
    
    /**
     * Builds a cookie string using the provided parameters.
     *
     * @param string         $name       The name of the cookie.
     * @param string         $value      The value of the cookie.
     * @param integer|string $expiryTime The expiry time in seconds since the epoch.
     * @param string         $domain     The domain of the cookie.
     * @param string         $path       The path of the cookie.
     * @param boolean        $secure     Whether the cookie can only be sent over encrypted messages.
     * @param boolean        $httpOnly   Whether the cookie can only be sent over the HTTP protocol.
     *
     * @return string The built cookie.
     */
    function build_cookie(string $name, string $value, $expiryTime, string $domain, string $path = "/",
                          bool $secure = true, bool $httpOnly = true) {
        if (!is_numeric($expiryTime)) {
            throw new \InvalidArgumentException("Parameter 'expiryTime' must be numeric.");
        }
        
        $cookie = "$name=$value; Expires=" . gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $expiryTime)
                  . "; Domain=$domain; Path=$path";
        if ($secure !== false) {
            $cookie .= "; Secure";
        }
        if ($httpOnly !== false) {
            $cookie .= "; HttpOnly";
        }
        
        return $cookie;
    }
    
    // TODO(Matthew): Try to find a way to not need to canonicalise anything.
    /**
     * Canonicalises a given action class path or name.
     *
     * @param string $action
     *
     * @return mixed
     */
    function canonicalise_action(string $action) {
        $parts = explode("\\", $action);
        $name = end($parts);
        
        return strtoupper(preg_replace("/\B([A-Z])/", "_$1", str_replace("Action", "", $name)));
    }
    
    /**
     * Canonicalises a given HTTP method.
     *
     * @param string $method
     *
     * @return mixed
     */
    function canonicalise_method(string $method) {
        return strtoupper($method);
    }
    
    /**
     * Handles a critical error. Typical handling method during bootstrapping.
     *
     * @param \Exception $exception The exception that led to the critical error.
     * @param bool       $exit      Whether to exit the program. Defaults to TRUE.
     */
    function critical_error(\Exception $exception, bool $exit = true) : void {
        error_log(
            "/////  CRITICAL ERROR  \\\\\\\\\\" . PHP_EOL . "Error Code: " . $exception->getCode() . PHP_EOL
            . "Error Location: " . $exception->getFile() . " : " . $exception->getLine() . PHP_EOL . "Error Message: "
            . $exception->getMessage() . PHP_EOL . "Stack Trace: " . PHP_EOL . $exception->getTraceAsString()
        );
        if ($exit) {
            exit();
        }
    }
