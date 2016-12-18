<?php
    namespace AlderTest\Alder;
    
    use Alder\DataContainer;
    
    use AlderTest\SimpleArrayAccessObject;
    
    use Symfony\Component\Filesystem\Filesystem;

    class A extends DataContainer {}
    class B extends DataContainer {}
    
    /**
     * Test functionality of the data container class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class DataContainerTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var \Symfony\Component\Filesystem\Filesystem $filesystem
         */
        protected $filesystem;
        
        public function setUp() {
            $this->filesystem = new Filesystem();
            
            $object = new SimpleArrayAccessObject();
            $object["test"] = "test";
            
            $this->filesystem->dumpFile(file_build_path(TEMP_DIRECTORY, "test.txt"), "test");
            $this->filesystem->dumpFile(file_build_path(TEMP_DIRECTORY, "empty_cache.txt"), serialize("cache"));
            $this->filesystem->dumpFile(file_build_path(TEMP_DIRECTORY, "filled_cache.txt"), serialize($object));
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::create
         */
        public function createReturnsInstance() {
            $container = DataContainer::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "cache.txt"));
            
            $this->assertTrue($container instanceof DataContainer);
        }
    
        /**
         * @test
         *
         * @covers \Alder\DataContainer::create
         */
        public function createReturnsSameInstanceForSameSubclass() {
            $aContainer = A::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "empty_cache.txt"));
            $anotherAContainer = A::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "empty_cache.txt"));
        
            $this->assertTrue($aContainer === $anotherAContainer);
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::create
         */
        public function createReturnsUniqueInstanceForDifferentSubclasses() {
            $aContainer = A::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "empty_cache.txt"));
            $bContainer = B::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "empty_cache.txt"));
            
            $this->assertFalse($aContainer === $bContainer);
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::create
         */
        public function containerPutsDefaultsIntoCacheIfNoCacheOnCreate() {
            DataContainer::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "fake_cache.txt"));
            
            $this->assertTrue(file_get_contents(file_build_path(TEMP_DIRECTORY, "fake_cache.txt")) === "test");
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::get
         */
        public function containerGetsFromCacheBeforeDefaultDataSource() {
            $container = DataContainer::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "empty_cache.txt"));
            
            $this->assertTrue($container->get() === "cache");
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::get
         */
        public function containerGetsFromDefaultDataSourceIfNoCache() {
            $container = DataContainer::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "fake_cache.txt"));
            
            $this->assertTrue($container->get() === "test");
        }
        
        /**
         * @test
         *
         * @covers \Alder\DataContainer::get
         */
        public function containerUneserialisesObjectsInCacheCorrectly() {
            $container = DataContainer::create(file_build_path(TEMP_DIRECTORY, "test.txt"), file_build_path(TEMP_DIRECTORY, "filled_cache.txt"));
    
            $this->assertTrue($container->get() instanceof SimpleArrayAccessObject);
        }
    }
