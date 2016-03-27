<?php
    namespace SycamoreTest\Sycamore\OS;
    
    use Sycamore\OS\FileSystem;
    
    use SycamoreTest\TestHelpers;
    
    /**
     * Test functionality of Sycamore's FileSystem class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class FileSystemTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Create files for destruction in tests.
         */
        protected function setUp()
        {
            // Create directories for tests.
            mkdir(TEMP_DIRECTORY . "/emptyDirTest");
            mkdir(TEMP_DIRECTORY . "/filledDirTest");
            
            // Create files for test.
            file_put_contents(TEMP_DIRECTORY . "/individualFile.txt", "test");
            file_put_contents(TEMP_DIRECTORY . "/filledDirTest/someFile.txt", "test");
        }
        
        /**
         * Nuke the temporary directory after tests have ran.
         */
        protected function tearDown()
        {
            TestHelpers::nukeDirectory(TEMP_DIRECTORY);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteIndividualFileTest()
        {
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . "/individualFile.txt"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteEmptyDirectoryTest()
        {
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . "/emptyDirTest"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteDirectoryWithFileContentsTest()
        {
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . "/filledDirTest", true));
        }
    }
