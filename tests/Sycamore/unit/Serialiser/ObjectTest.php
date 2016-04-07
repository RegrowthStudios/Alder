<?php
    namespace SycamoreTest\Sycamore\Serialiser;
    
    use Sycamore\Serialiser\Object;
    
    /**
     * Test class for creating basic object from.
     */
    class ObjectTestObject
    {
        public function test()
        {
            return "test";
        }
        
        protected $testField = "testField";
    }
    
    /**
     * Test functionality of Sycamore's Object data serialiser class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ObjectTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Serialiser\Object::encode
         * @covers \Sycamore\Serialiser\Object::decode
         * @covers \Sycamore\Serialiser\Object::getSerialiser
         */
        public function serialiserReturnsInitialDataAfterEncodeDecodeCycleTest()
        {            
            $encodedData = Object::encode(new ObjectTestObject());
            
            $decodedData = Object::decode($encodedData);
            
            $this->assertEquals(new ObjectTestObject(), $decodedData);
        }
    }
