<?php
    
    namespace Alder\Install\Operation;

    use \Alder\DiContainer;

    use \Zend\Config\Config;
    use \Zend\Config\Writer\PhpArray as ConfigWriter;
    use \Zend\ConfigAggregator\ConfigAggregator;
    use \Zend\ConfigAggregator\PhpFileProvider;
    use \Zend\Stdlib\ArrayUtils;
    
    /**
     * Class providing the default installation behaviour for Alder modules.
     * 
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0 
     */
    class Install implements InstallInterface
    {
        /**
         * The default installation procedure for Alder modules.
         */
        public static function run(string $moduleName) {
            // TODO(Matthew): Investigate how we can support user intervention in this process.
            //                E.g. staged, multiple HTTP request, process.
            static::doSrcStage($moduleName);

            static::doConfigStage($moduleName);

            static::doDatabaseStage($moduleName);

            static::doTasksStage($moduleName);
        }

        protected static function doSrcStage(string $moduleName) {
            $sourceDir      = file_build_path(INSTALL_DATA_DIRECTORY, $moduleName, "src", "*");
            $destinationDir = SRC_DIRECTORY;

            exec("yes | cp -rf $sourceDir $destinationDir");
            exec("yes | rm -rf $sourceDir");
        }

        protected static function doConfigureDefaults(string $moduleName) : array {
            // TODO(Matthew): Implement this to be declarative for module authors by default.
            return [];
        }

        protected static function doConfigStage(string $moduleName) {
            $patch = static::doConfigureDefaults($moduleName);

            $sourceDir      = file_build_path(INSTALL_CONFIG_DIRECTORY, $moduleName);
            $destinationDir = file_build_path(CONFIG_DIRECTORY, "autoload");

            // Aggregate defaults and merge patch.
            // Production
            $providerString = "{{,*.}global,{,*.}production,{,*.}local}.php";
            $configManager = new ConfigAggregator([
                new PhpFileProvider(file_build_path($sourceDir, $providerString))
            ]);
            $productionConfig = ArrayUtils::merge($configManager->getMergedConfig(), $patch);
            $productionConfig = Config($productionConfig);

            // Development
            $providerString = "{{,*.}global,{,*.}development,{,*.}local}.php";
            $configManager = new ConfigAggregator([
                new PhpFileProvider(file_build_path($sourceDir, $providerString))
            ]);
            $developmentConfig = $configManager->getMergedConfig();
            $developmentConfig = Config($developmentConfig);

            // Write to file.
            $writer = new ConfigWriter();
            file_put_contents(file_build_path($destinationDir, "$moduleName.production.config.php"),  $writer->toString($productionConfig));
            file_put_contents(file_build_path($destinationDir, "$moduleName.development.config.php"), $writer->toString($developmentConfig));
        }

        protected static function doDatabaseStage(string $moduleName) {
            $config = require file_build_path(CONFIG_DIRECTORY, "config.php");

            $db = $config->alder->db->adapter;

            $sqlScriptDir = file_build_path(INSTALL_DATA_DIRECTORY, $moduleName, "sql");
            foreach (new \DirectoryIterator(sqlScriptDir) as $file) {
                if ($file->isFile()) {
                    exec("mysql --username=" . $db->username
                            . " --password=" . $db->password
                            . " --database=" . $db->database
                            . " --host="     . $db->hostname
                            . " --port="     . $db->port
                            . " < " . $file->getPathname());
                }
            }
        }
    }
