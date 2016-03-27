<?php
    namespace SycamoreTest\Sycamore\Config;
    
    use Sycamore\Config\ConfigUtils;
    
    use SycamoreTest\TestHelpers;
    
    /**
     * Test functionality of Sycamore's TableCache class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ConfigUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Remove all temporarily generated files.
         */
        protected function tearDown()
        {
            TestHelpers::nukeDirectory(TEMP_DIRECTORY);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\TableCacheTest::save
         */
        public function configFileGeneratesCorrectlyTest()
        {
            $config = [
                "test"
            ];
            
            $filepath = TEMP_DIRECTORY . "/conf.php";
            
            ConfigUtils::save($filepath, $config);
            
            $result = require $filepath;
            
            $this->assertEquals($config, $result);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Config\ConfigUtils::save
         */
        public function configFileOverwritesCorrectlyTest()
        {
            $config = [
                "test"
            ];
            $config2 = [
                "test2"
            ];
            
            $filepath = TEMP_DIRECTORY . "/conf.overwrite.php";
            
            ConfigUtils::save($filepath, $config);
            ConfigUtils::save($filepath, $config2);
            
            $result = require $filepath;
            
            $this->assertEquals($config2, $result);
        }
    }
    