<?php

    use \Zend\ConfigAggregator\ConfigAggregator;
    use \Zend\ConfigAggregator\PhpFileProvider;
    
    require "env.state.php";
    
    if (ENV == PROD) {
        $providerString = "*.production.config.php";
        $cacheFilename  = "production.config.cache.php";
    } else {
        $providerString = "*.development.config.php";
        $cacheFilename  = "development.config.cache.php";
    }

    $configManager = new ConfigAggregator([
        new PhpFileProvider(file_build_path(COMMON_CONFIG_DIRECTORY, $providerString))
    ], file_build_path(CACHE_DIRECTORY, "config", $cacheFilename));

    return new ArrayObject($configManager->getMergedConfig());
