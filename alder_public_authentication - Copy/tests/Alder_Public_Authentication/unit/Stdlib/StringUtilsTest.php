<?php
    namespace AlderTest\Alder\Stdlib;
    
    use Alder\Stdlib\StringUtils;
    
    /**
     * Test functionality of the string utility functions class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class StringUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Stores a resource stream to temp/test.txt
         *
         * @var resource
         */
        protected $testFile;
        
        /**
         * Sets up a temporary file and stream for testing purposes.
         */
        public function setUp()
        {
            file_put_contents(file_build_path(TEMP_DIRECTORY, "test.txt"), "test");
            
            $this->testFile = fopen(file_build_path(TEMP_DIRECTORY, "test.txt"), "r");
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::convertToString
         */
        public function convertToStringWorksWithAllDataTypesTest()
        {
            $object = new \stdClass();
            $object->foo = "bar";
            
            $data = [
                1,
                "hello",
                2.3,
                true,
                NULL,
                $object,
                "test" => 5
            ];
            
            $this->assertTrue(is_string(StringUtils::convertToString($data)));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::convertToString
         */
        public function convertToStringIsDeterministicTest()
        {
            $object = new \stdClass();
            $object->foo = "bar";
            
            $data = [
                1,
                "hello",
                2.3,
                true,
                NULL,
                $object,
                "test" => 5
            ];
            
            $this->assertEquals(StringUtils::convertToString($data), StringUtils::convertToString($data));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::convertToString
         */
        public function convertToStringThrowsExceptionForResourceTypeTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            StringUtils::convertToString($this->testFile);
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::convertToString
         */
        public function convertToStringThrowsExceptionForUnknownTypeTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            fclose($this->testFile);
            StringUtils::convertToString($this->testFile);
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::endsWith
         */
        public function endsWithReturnsTrueIfStringEndsWithProvidedNeedleTest()
        {
            $substring = "test";
            $string = "this is a test";
            
            $this->assertTrue(StringUtils::endsWith($string, $substring));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::endsWith
         */
        public function endsWithReturnsFalseIfStringDoesNotEndWithProvidedNeedleTest()
        {
            $substring = "test";
            $string = "this is a test.";
            
            $this->assertFalse(StringUtils::endsWith($string, $substring));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\StringUtils::endsWith
         */
        public function endsWithReturnsFalseForStringShorterThanNeedleTest()
        {
            $substring = "extra long test";
            $string = "test";
            
            $this->assertFalse(StringUtils::endsWith($string, $substring));
        }
    }
