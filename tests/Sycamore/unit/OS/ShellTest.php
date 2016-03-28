<?php
    namespace SycamoreTest\Sycamore\OS;
    
    use Sycamore\OS\Shell;
    
    use SycamoreTest\TestHelpers;
    
    /**
     * Test functionality of Sycamore's FileSystem class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ShellTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Clear out temp directory.
         */
        protected function tearDown()
        {
            TestHelpers::nukeDirectory(TEMP_DIRECTORY);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\Shell::execute
         */
        public function commandReturnsOutputTest()
        {
            $output = [];
            
            Shell::execute("echo Hello World!", $output);
            
            $this->assertEquals("Hello World!", $output[0]);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\Shell::execute
         */
        public function commandCorrectlyExecutesTest()
        {
            // If anyone knows a common variation on this to both Windows and UNIX-like shells, please tell me!
            if (OS == WINDOWS) {
                $data = '^<?php return [ "test" ];';
                $dataPhp = [
                    "test"
                ];

                Shell::execute("echo $data > " . TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "test.php");

                $returnedData = require TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "test.php";

                $this->assertEquals($dataPhp, $returnedData);
            } else if (OS == UNIX) {
                $data = "<?php return [ \"test\" ];";
                $dataPhp = [
                    "test"
                ];

                Shell::execute('echo "' . $data . '" > ' . TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "test.php");

                $returnedData = require TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "test.php";

                $this->assertEquals($dataPhp, $returnedData);
            }
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\Shell::execute
         */
        public function executeFailsOnBadCommandTypeTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            Shell::execute(4);
        }
    }
