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
