<?php

    use Zend\Expressive\ConfigManager\ConfigManager;
    use Zend\Expressive\ConfigManager\PhpFileProvider;

    require "env.state.php";
    
    $providerString = "{{,*.}global";
    if (ENV == PRODUCTION) {
        $providerString .= ",{,*.}production";
    } else {
        $providerString .= ",{,*.}development";
    }
    $providerString .= ",{,*.}local}.php";
    
    $configManager = new ConfigManager([
        new PhpFileProvider(file_build_path(CONFIG_DIRECTORY, "autoload", $providerString))
    ], file_build_path(CACHE_DIRECTORY, "config", "cachedConfig.php"));
    
    return new ArrayObject($configManager->getMergedConfig());
