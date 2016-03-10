<?php

/* 
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    // Dynamically determine modules installed.
    $moduleDirs = glob(MODULES_DIRECTORY."/*", GLOB_ONLYDIR);
    foreach($moduleDirs as $key => $moduleDir) {
        $explode = explode("/", $moduleDir);
        $moduleDirs[$key] = end($explode);
    }
    
    return array (
        "modules" => $moduleDirs,
        "module_listener_options" => array (
            "module_paths" => array (
                MODULES_DIRECTORY
            ),
            "config_glob_paths" => array (
                sprintf(CONFIG_DIRECTORY . "/autoload/{,*.}{global,%s,local}.php", ENV),
            ),
            "config_cache_enabled" => (ENV == PRODUCTION),
            "config_cache_key" => "sycamore_config",
            "module_map_cache_enabled" => (ENV == PRODUCTION),
            "module_map_cache_key" => "sycamore_module_map",
            "cache_dir" => CACHE_DIRECTORY . "/config",
            "check_dependencies" => (ENV != PRODUCTION),
        ),
    );
