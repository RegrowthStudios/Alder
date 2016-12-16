<?php
    namespace AlderTest\Alder\Stdlib;
    
    use Alder\Stdlib\CacheUtils;
        
    /**
     * Test functionality of the cache utility functions class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class CacheUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         *
         * @covers \Alder\Stdlib\CacheUtils::generateCacheAddress
         */
        public function generateCacheAddressReturnsString() {
            $result1 = CacheUtils::generateCacheAddress("test");
            $result2 = CacheUtils::generateCacheAddress("test", "another", "andAnother");
            
            $this->assertTrue(is_string($result1));
            $this->assertTrue(is_string($result2));
        }
    }
