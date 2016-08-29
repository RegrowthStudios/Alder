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
            mkdir(file_build_path(TEMP_DIRECTORY, "emptyDirTest"));
            mkdir(file_build_path(TEMP_DIRECTORY, "filledDirTest"));
            
            // Create files for test.
            file_put_contents(file_build_path(TEMP_DIRECTORY, "individualFile.txt"), "test");
            file_put_contents(file_build_path(TEMP_DIRECTORY, "filledDirTest", "someFile.txt"), "test");
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
        public function nonExistentFileOrDirectoryDeletionReturnsFalse()
        {
            $this->assertFalse(FileSystem::delete(file_build_path(TEMP_DIRECTORY, "123g324hb", "asd34t54A")));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteIndividualFileTest()
        {
            $this->assertTrue(FileSystem::delete(file_build_path(TEMP_DIRECTORY, "individualFile.txt")));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteEmptyDirectoryTest()
        {
            $this->assertTrue(FileSystem::delete(file_build_path(TEMP_DIRECTORY, "emptyDirTest")));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canNotDeleteDirectoryWithFileContentsWithForceFalseTest()
        {
            $this->assertFalse(FileSystem::delete(file_build_path(TEMP_DIRECTORY, "filledDirTest"), false));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::delete
         */
        public function canDeleteDirectoryWithFileContentsWithForceTrueTest()
        {
            $this->assertTrue(FileSystem::delete(file_build_path(TEMP_DIRECTORY, "filledDirTest"), true));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function canCreateFileInExistingDirectoryTest()
        {
            $this->assertTrue((bool) FileSystem::filePutContents(file_build_path(TEMP_DIRECTORY, "existingDirWriteTest.txt"), "test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function canCreateFileInNonExistentDirectoryTest()
        {
            $this->assertTrue((bool) FileSystem::filePutContents(file_build_path(TEMP_DIRECTORY, "nonExistingDir", "writeTest.txt"), "test"));
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
            
            FileSystem::filePutContents(file_build_path(TEMP_DIRECTORY, "testContents.php"), $data);
            
            $returnedData = require file_build_path(TEMP_DIRECTORY, "testContents.php");
            
            $this->assertEquals($dataPhp, $returnedData);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\OS\FileSystem::filePutContents
         */
        public function appendToExistingFilesYieldsCorrectFileContentsTest()
        {
            $data1 = "<?php return ";
            $data2 = "[ \"test\" ];";
            $dataPhp = [
                "test"
            ];
            
            FileSystem::filePutContents(file_build_path(TEMP_DIRECTORY, "testAppendingContents.php"), $data1);
            FileSystem::filePutContents(file_build_path(TEMP_DIRECTORY, "testAppendingContents.php"), $data2, FILE_APPEND);
            
            $returnedData = require file_build_path(TEMP_DIRECTORY, "testAppendingContents.php");
            
            $this->assertEquals($dataPhp, $returnedData);
        }
    }
