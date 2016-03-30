<?php
    namespace SycamoreTest\Sycamore\Serialiser;
    
    use Sycamore\Stdlib\StringUtils;
    
    /**
     * Test functionality of Sycamore's string utility functions class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class StringUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\StringUtils::convertToString
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
                $object
            ];
            
            $this->assertTrue(is_string(StringUtils::convertToString($data)));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\StringUtils::convertToString
         */
        public function convertToStringIsDeterministic()
        {
            $object = new \stdClass();
            $object->foo = "bar";
            
            $data = [
                1,
                "hello",
                2.3,
                true,
                NULL,
                $object
            ];
            
            $this->assertEquals(StringUtils::convertToString($data), StringUtils::convertToString($data));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\StringUtils::endsWith
         */
        public function endsWithReturnsTrueIfStringEndsWithProvidedNeedle()
        {
            $substring = "test";
            $string = "this is a test";
            
            $this->assertTrue(StringUtils::endsWith($string, $substring));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\StringUtils::endsWith
         */
        public function endsWithReturnsFalseIfStringDoesNotEndWithProvidedNeedle()
        {
            $substring = "test";
            $string = "this is a test.";
            
            $this->assertFalse(StringUtils::endsWith($string, $substring));
        }
    }
