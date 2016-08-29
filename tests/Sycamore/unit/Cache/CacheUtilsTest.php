<?php
    namespace SycamoreTest\Sycamore\Cache;
    
    use Sycamore\Cache\CacheUtils;
    
    /**
     * Test functionality of Sycamore's CacheUtils class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class CacheUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * Prepares a set of various possible where values to test against.
         */
        public function deterministicCacheAddressWhereProvider()
        {
            $objectWhere = new \stdClass;
            $objectWhere->test = 1;
            $objectWhere->other = "this";
            
            $wheres = [
                "stringWhere" => [
                    "test"
                ],
                "intWhere" => [
                    1
                ],
                "boolWhere" => [
                    true
                ],
                "arrayWhere" => [
                    [ 1, "test", false ]
                ],
                "objectWhere" => [
                    $objectWhere
                ]
            ];
            
            return $wheres;
        }
        
        /**
         * @test
         * 
         * @dataProvider deterministicCacheAddressWhereProvider
         * 
         * @covers \Sycamore\Cache\CacheUtils::generateCacheAddress
         */
        public function generateCacheAddressIsDeterministicTest($where)
        {
            $result1 = CacheUtils::generateCacheAddress("test", $where);
            $result2 = CacheUtils::generateCacheAddress("test", $where);
            
            $this->assertEquals($result1, $result2);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\CacheUtils::generateCacheAddress
         */
        public function generateCacheAddressThrowsExceptionForNoneStringLocationTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            CacheUtils::generateCacheAddress(2, "test");
        }
    }
