<?php

    use \Zend\ConfigAggregator\ConfigAggregator;
    use \Zend\ConfigAggregator\PhpFileProvider;
    
    require "env.state.php";
    
    if (ENV == PROD) {
        $providerString = "*.production.php";
        $cacheFilename  = "production.php";
    } else {
        $providerString = "*.development.php";
        $cacheFilename  = "development.php";
    }

    $configManager = new ConfigAggregator([
        new PhpFileProvider(file_build_path(CONFIG_DIRECTORY, $providerString))
    ], file_build_path(CACHE_DIRECTORY, "config", $cacheFilename));

    return new ArrayObject($configManager->getMergedConfig());
