<?php
    namespace SycamoreTest\Sycamore\Stdlib;
    
    use Sycamore\Stdlib\UniqueID;
    
    /**
     * Test functionality of Sycamore's unique ID class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class UniqueIDTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\UniqueID::generate
         */
        public function uniqueIdPrefixIsAsSupplied()
        {
            $uniqueid = UniqueID::generate("test");
            
            $this->assertSame(0, strpos($uniqueid, "test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\UniqueID::generate
         */
        public function uniqueIdReturnsCorrectLengths()
        {
            $uniqueidShort = UniqueID::generate("", false, UniqueID::SHORT);
            $uniqueidMedium = UniqueID::generate("", false, UniqueID::MEDIUM);
            $uniqueidLong = UniqueID::generate("", false, UniqueID::LONG);
            
            $this->assertSame(16, strlen($uniqueidShort));
            $this->assertSame(19, strlen($uniqueidMedium));
            $this->assertSame(32, strlen($uniqueidLong));
        }
    }
