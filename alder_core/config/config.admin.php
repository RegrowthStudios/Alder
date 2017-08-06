<?php

    use \Zend\ConfigAggregator\ConfigAggregator;
    use \Zend\ConfigAggregator\PhpFileProvider;
    
    require "env.state.php";
    
    $providerString = "*.{global,";
    if (ENV == PROD) {
        $providerString .= "production";
        $cacheFilename  = "production.admin.php";
    } else {
        $providerString .= "development";
        $cacheFilename  = "development.admin.php";
    }
    $providerString .= ",local}.config.php";

    $configManager = new ConfigAggregator([
        new PhpFileProvider(file_build_path(COMMON_CONFIG_DIRECTORY, $providerString)),
        new PhpFileProvider(file_build_path(ADMIN_CONFIG_DIRECTORY, $providerString))
    ], file_build_path(CACHE_DIRECTORY, "config", $cacheFilename));

    return new ArrayObject($configManager->getMergedConfig());