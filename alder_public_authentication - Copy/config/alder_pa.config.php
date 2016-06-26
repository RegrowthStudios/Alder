<?php

    // Dynamically determine modules installed.
    $moduleDirs = glob(MODULES_DIRECTORY."/*", GLOB_ONLYDIR);
    foreach($moduleDirs as $key => $moduleDir) {
        $explode = explode("/", $moduleDir);
        $moduleDirs[$key] = end($explode);
    }
    
    return [
        "modules" => $moduleDirs,
        "module_listener_options" => [
            "module_paths" => [
                MODULES_DIRECTORY
            ],
            "config_glob_paths" => [
                sprintf(CONFIG_DIRECTORY . "/autoload/{,*.}{global,%s,local}.php", ENV),
            ],
            "config_cache_enabled" => (ENV == PRODUCTION),
            "config_cache_key" => "sycamore_config",
            "module_map_cache_enabled" => (ENV == PRODUCTION),
            "module_map_cache_key" => "sycamore_module_map",
            "cache_dir" => CACHE_DIRECTORY . "/config",
            "check_dependencies" => (ENV != PRODUCTION),
        ],
    ];
