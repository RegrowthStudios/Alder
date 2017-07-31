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

    /**
     * Diffs the first two arrays recursively, then does the same for the result of the previous two
     * diff'd arrays and the subsequent array.
     *
     * @param array $array1
     * @param array ...$arrays
     *
     * @return array
     */
    function array_diff_assoc_recursive(array $array1, array ...$arrays) : array {
        $difference = [];
        $firstRun   = true;

        // Iterate over all the comparative arrays, and all of the 1st level keys/values of initially 
        // the first array then of the diff'd array, comparing with each of the comparative arrays and 
        // adding to the calculated diff.
        foreach ($arrays as $array) {
            foreach (($firstRun ? $array1 : $difference) as $key => $value) {
                if (is_array($value)) {
                    // If the value being considered in the first array is itself an array, 
                    // then if the currently considered comparative array does not contain an array
                    // at that key then just add that value to the diff. Otherwise, apply this algorithm
                    // to the diff of the value and the currently considered comparative array's value at the 
                    // same key.
                    if (!isset($array[$key]) || !is_array($array[$key])) {
                        $difference[$key] = $value;
                    } else {
                        $new_diff = array_diff_assoc_recursive($value, $array[$key]);

                        // If we got a diff, then add it.
                        if (!empty($new_diff)) {
                            $difference[$key] = $new_diff;
                        }
                    }
                } else if (!array_key_exists($key, $array) || $array[$key] !== $value) {
                    // If the value being considered in the first array is not an array and
                    // either the current considered comparative array has no value at that key,
                    // or the value is different, then add that to the diff.
                    $difference[$key] = $value;
                }
                $firstRun = false;
            }
        }

        return $difference;
    }
