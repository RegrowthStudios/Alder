<?php
    namespace SycamoreTest\Sycamore\Cache;
    
    use Sycamore\Cache\TableCache;
    use Sycamore\Db\Table\User;
    
    use SycamoreTest\Bootstrap;
    
    /**
     * Test functionality of Sycamore's TableCache class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class TableCacheTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\TableCache::__construct
         */
        public function tableCacheRequiresStringNamespaceTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            $this->getMockBuilder(TableCache::class)
                    ->setConstructorArgs([Bootstrap::getServiceManager(), 2])
                    ->getMock();
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\TableCache::fetchTable
         */
        public function fetchNonExistentTableThrowsExceptionTest()
        {
            $tableCache = $this->getMockBuilder(TableCache::class)
                    ->setMethods(NULL)
                    ->setConstructorArgs([Bootstrap::getServiceManager(), "Sycamore\\Db\\Table\\"])
                    ->getMock();
            
            $this->expectException(\InvalidArgumentException::class);
            
            $tableCache->fetchTable("ZYX");
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\TableCache::fetchTable
         */
        public function fetchExistingTableReturnsTableObjectTest()
        {
            $tableCache = $this->getMockBuilder(TableCache::class)
                    ->setMethods(NULL)
                    ->setConstructorArgs([Bootstrap::getServiceManager(), "Sycamore\\Db\\Table\\"])
                    ->getMock();
                        
            $table = $tableCache->fetchTable("User");
            
            $this->assertInstanceOf(User::class, $table);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Cache\TableCache::fetchTable
         */
        public function fetchPreviouslyFetchedTableReturnedFromCacheTest()
        {
            $tableCache = $this->getMockBuilder(TableCache::class)
                    ->setMethods(NULL)
                    ->setConstructorArgs([Bootstrap::getServiceManager(), "Sycamore\\Db\\Table\\"])
                    ->getMock();
            
            $table1 = $tableCache->fetchTable("User");
            $table2 = $tableCache->fetchTable("User");
            $table3 = new User(Bootstrap::getServiceManager());
            
            $this->assertSame($table1, $table2);
            $this->assertNotSame($table3, $table2);
        }
    }
