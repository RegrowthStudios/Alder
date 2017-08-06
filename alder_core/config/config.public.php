<?php

    use \Zend\ConfigAggregator\ConfigAggregator;
    use \Zend\ConfigAggregator\PhpFileProvider;
    
    require "env.state.php";
    
    $providerString = "*.{global,";
    if (ENV == PROD) {
        $providerString .= "production";
        $cacheFilename  = "production.public.php";
    } else {
        $providerString .= "development";
        $cacheFilename  = "development.public.php";
    }
    $providerString .= ",local}.config.php";

    $configManager = new ConfigAggregator([
        new PhpFileProvider(file_build_path(COMMON_CONFIG_DIRECTORY, $providerString)),
        new PhpFileProvider(file_build_path(PUBLIC_CONFIG_DIRECTORY, $providerString))
    ], file_build_path(CACHE_DIRECTORY, "config", $cacheFilename));

    return new ArrayObject($configManager->getMergedConfig());
