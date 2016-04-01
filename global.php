<?php

    /**
     * Builds a file path from the given segments using DIRECTORY_SEPARATOR.
     * 
     * @param variadic $segments The segments to build the path from.
     */
    function file_build_path(...$segments)
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }