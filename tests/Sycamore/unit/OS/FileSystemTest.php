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
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "individualFile.txt"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteEmptyDirectoryTest()
        {
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "emptyDirTest"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteDirectoryWithFileContentsTest()
        {
            $this->assertTrue(FileSystem::delete(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "filledDirTest", true));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function canCreateFileInExistingDirectoryTest()
        {
            $this->assertTrue((bool) FileSystem::filePutContents(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "existingDirWriteTest.txt", "test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function canCreateFileInNonExistentDirectoryTest()
        {
            $this->assertTrue((bool) FileSystem::filePutContents(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "nonExistingDir" . DIRECTORY_SEPARATOR . "writeTest.txt", "test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function createsFilesWithCorrectDataTest()
        {
            $data = "<?php return [ \"test\" ];";
            $dataPhp = [
                "test"
            ];
            
            FileSystem::filePutContents(TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "testContents.php", $data);
            
            $returnedData = require TEMP_DIRECTORY . DIRECTORY_SEPARATOR . "testContents.php";
            
            $this->assertEquals($dataPhp, $returnedData);
        }
    }
