<?php
    namespace SycamoreTest\Sycamore\Serialiser;
    
    use Sycamore\Serialiser\API;
    
    /**
     * Test functionality of Sycamore's API data serialiser class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class APITest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Serialiser\API::encode
         * @covers \Sycamore\Serialiser\API::decode
         */
        public function serialiserReturnsInitialDataAfterEncodeDecodeCycleTest()
        {
            $data = [
                "test" => "hello"
            ];
            
            $encodedData = API::encode($data);
            
            $this->assertEquals($data, API::decode($encodedData));
        }
    }
