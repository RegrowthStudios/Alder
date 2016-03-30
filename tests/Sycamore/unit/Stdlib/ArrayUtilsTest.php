<?php
    namespace SycamoreTest\Sycamore\Serialiser;
    
    use Sycamore\Stdlib\ArrayUtils;
    
    /**
     * Simple traversable object for testing with.
     */
    class SimpleTraversableObject implements \Iterator
    {
        protected $position = 0;
        protected $array = array(
            "test",
            "hello",
            "world",
        );
        
        public function __construct()
        {
            $this->position = 0;
        }

        public function rewind()
        {
            $this->position = 0;
        }

        public function current()
        {
            return $this->array[$this->position];
        }

        public function key()
        {
            return $this->position;
        }

        public function next()
        {
            ++$this->position;
        }

        public function valid()
        {
            return isset($this->array[$this->position]);
        }
        
        public function &toArray()
        {
            return $this->array;
        }
        
        public function __toString()
        {
            return implode($this->array);
        }
    }
    
    /**
     * Simple array access object for testing with.
     */
    class SimpleArrayAccessObject implements \ArrayAccess
    {
        protected $container = array();

        public function __construct()
        {
            $this->container = array(
                "one"   => 1,
                "two"   => 2,
                "three" => 3,
            );
        }

        public function offsetSet($offset, $value)
        {
            if (is_null($offset)) {
                $this->container[] = $value;
            } else {
                $this->container[$offset] = $value;
            }
        }

        public function offsetExists($offset)
        {
            return isset($this->container[$offset]);
        }

        public function offsetUnset($offset)
        {
            unset($this->container[$offset]);
        }

        public function offsetGet($offset)
        {
            return isset($this->container[$offset]) ? $this->container[$offset] : null;
        }
    }
    
    /**
     * Test functionality of Sycamore's Array utility functions class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::inArrayRecursive
         */
        public function inArrayRecursiveWorksForArbitraryArrayDepthTest()
        {
            $array1 = [ "test" ];
            $array2 = [ $array1 ];
            $array3 = [ $array2 ];
            $array4 = [ $array3 ];
            
            $this->assertTrue(ArrayUtils::inArrayRecursive("test", $array1));
            $this->assertTrue(ArrayUtils::inArrayRecursive("test", $array2));
            $this->assertTrue(ArrayUtils::inArrayRecursive("test", $array3));
            $this->assertTrue(ArrayUtils::inArrayRecursive("test", $array4));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::arrayKeyExistsRecursive
         */
        public function arrayKeyExistsRecursiveWorksForArbitraryArrayDepthTest()
        {
            $array1 = [ "test" => 1 ];
            $array2 = [ $array1 ];
            $array3 = [ $array2 ];
            $array4 = [ $array3 ];
            
            $this->assertTrue(ArrayUtils::arrayKeyExistsRecursive("test", $array1));
            $this->assertTrue(ArrayUtils::arrayKeyExistsRecursive("test", $array2));
            $this->assertTrue(ArrayUtils::arrayKeyExistsRecursive("test", $array3));
            $this->assertTrue(ArrayUtils::arrayKeyExistsRecursive("test", $array4));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::recursiveAsort
         */
        public function recursiveAsortReturnsTrueOnSuccessfulSortTest()
        {
            $array = [ 1, 4, 3, 2 ];
            
            $this->assertTrue(ArrayUtils::recursiveAsort($array));
            
            $this->assertEquals([0, 3, 2, 1], array_keys($array));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::recursiveAsort
         */
        public function recursiveAsortWorksForArbitraryArrayDepthTest()
        {
            $array1 = [ 1, 4, 3, 2 ];
            $array2 = [ 2, 1, 4, $array1 ];
            $array3 = [ 10, 5, 3, $array2 ];
            $array4 = [ 1, 5, 3, $array3 ];
            
            $sortedArray1Keys = [0, 3, 2, 1];
            $sortedArray2Keys = [1, 0, 2, 3];
            $sortedArray3Keys = [2, 1, 0, 3];
            $sortedArray4Keys = [0, 2, 1, 3];
            
            $this->assertTrue(ArrayUtils::recursiveAsort($array1));
            $this->assertTrue(ArrayUtils::recursiveAsort($array2));
            $this->assertTrue(ArrayUtils::recursiveAsort($array3));
            $this->assertTrue(ArrayUtils::recursiveAsort($array4));
            
            $this->assertEquals($sortedArray1Keys, array_keys($array1));
            
            $this->assertEquals($sortedArray2Keys, array_keys($array2));
            $this->assertTrue(is_array($array2[3]));
            $this->assertEquals($sortedArray1Keys, array_keys($array2[3]));
            
            $this->assertEquals($sortedArray3Keys, array_keys($array3));
            $this->assertTrue(is_array($array3[3]));
            $this->assertEquals($sortedArray2Keys, array_keys($array3[3]));
            $this->assertTrue(is_array($array3[3][3]));
            $this->assertEquals($sortedArray1Keys, array_keys($array3[3][3]));
            
            
            $this->assertEquals($sortedArray4Keys, array_keys($array4));
            $this->assertTrue(is_array($array4[3]));
            $this->assertEquals($sortedArray3Keys, array_keys($array4[3]));
            $this->assertTrue(is_array($array4[3][3]));
            $this->assertEquals($sortedArray2Keys, array_keys($array4[3][3]));
            $this->assertTrue(is_array($array4[3][3][3]));
            $this->assertEquals($sortedArray1Keys, array_keys($array4[3][3][3]));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::recursiveKsort
         */
        public function recursiveKsortReturnsTrueOnSuccessfulSortTest()
        {
            $array = [ "a" => 1, "c" => 2, "b" => 3, "d" => 4 ];
            
            $this->assertTrue(ArrayUtils::recursiveKsort($array));
            
            $this->assertEquals(["a", "b", "c", "d"], array_keys($array));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::recursiveAsort
         */
        public function recursiveKsortWorksForArbitraryArrayDepthTest()
        {
            $array1 = [ "a" => 1, "c" => 2, "b" => 3, "d" => 4 ];
            $array2 = [ "c" => 2, "a" => 4, "b" => $array1, "d" => 2 ];
            $array3 = [ "b" => 2, "a" => 4, "c" => $array2, "d" => 2 ];
            $array4 = [ "c" => 2, "a" => 4, "d" => $array3, "b" => 2 ];
            
            $sortedArrayKeys = ["a", "b", "c", "d"];
            
            $this->assertTrue(ArrayUtils::recursiveKsort($array1));
            $this->assertTrue(ArrayUtils::recursiveKsort($array2));
            $this->assertTrue(ArrayUtils::recursiveKsort($array3));
            $this->assertTrue(ArrayUtils::recursiveKsort($array4));
            
            $this->assertEquals($sortedArrayKeys, array_keys($array1));
            
            $this->assertEquals($sortedArrayKeys, array_keys($array2));
            $this->assertTrue(is_array($array2["b"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array2["b"]));
            
            $this->assertEquals($sortedArrayKeys, array_keys($array3));
            $this->assertTrue(is_array($array3["c"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array3["c"]));
            $this->assertTrue(is_array($array3["c"]["b"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array3["c"]["b"]));
            
            
            $this->assertEquals($sortedArrayKeys, array_keys($array4));
            $this->assertTrue(is_array($array4["d"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array4["d"]));
            $this->assertTrue(is_array($array4["d"]["c"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array4["d"]["c"]));
            $this->assertTrue(is_array($array4["d"]["c"]["b"]));
            $this->assertEquals($sortedArrayKeys, array_keys($array4["d"]["c"]["b"]));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::validateArrayLike
         */
        public function validateArrayLikeValidatesArraysTest()
        {
            $array = [ 1, 2, 3, "test" ];
            
            $arrayReturned = ArrayUtils::validateArrayLike($array);
            
            $this->assertSame($array, $arrayReturned);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::validateArrayLike
         */
        public function validateArrayLikeValidatesTraversableObjectsTest()
        {
            $traversableObject = new SimpleTraversableObject();
            
            $arrayReturned = ArrayUtils::validateArrayLike($traversableObject);
            
            $this->assertSame($traversableObject->toArray(), $arrayReturned);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::validateArrayLike
         */
        public function validateArrayLikeValidatesArrayAccessObjectsTest()
        {
            $arrayAccessObject = new SimpleArrayAccessObject();
            
            $objectReturned = ArrayUtils::validateArrayLike($arrayAccessObject);
            
            $this->assertSame($arrayAccessObject, $objectReturned);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::validateArrayLike
         */
        public function validateArrayLikeValidatesArraysAndTraversableObjectsOnlyIfArrayOnlyFlagTrueTest()
        {
            $array = [ 1, 2, 3, "test" ];
            $traversableObject = new SimpleTraversableObject();
            $arrayAccessObject = new SimpleArrayAccessObject();
            
            $this->expectException(\InvalidArgumentException::class);
            
            $arrayReturned1 = ArrayUtils::validateArrayLike($array, NULL, true);
            $arrayReturned2 = ArrayUtils::validateArrayLike($traversableObject, NULL, true);
            ArrayUtils::validateArrayLike($arrayAccessObject, NULL, true);
            
            $this->assertSame($array, $arrayReturned1);
            $this->assertSame($traversableObject->toArray(), $arrayReturned2);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::xorArrays
         */
        public function xorArraysOperatesCorrectlyTest()
        {
            $array1 = [ 1, 2, 10, 3 ];
            $array2 = [ 1, 3, 11, 5 ];
            
            $arrayXorExpected = [ 10, 11, 5 ];            
            $arrayXorResult = ArrayUtils::xorArrays($array1, $array2);
            
            $this->assertEmpty(array_diff($arrayXorExpected, $arrayXorResult));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Stdlib\ArrayUtils::xorArrays
         */
        public function xorArraysHandlesMultipleValueTypesTest()
        {
            $array1 = [ 1, "hello", false, new SimpleTraversableObject()];
            
            $traversableObject = new SimpleTraversableObject();
            $traversableObjectContents = $traversableObject->toArray();
            $traversableObjectContents[] = "newitem";
            $array2 = [ 1, "world", 2, true, false, $traversableObject, new SimpleTraversableObject()];
            
            $expectedResultOfXor = [ "hello", "world", 2 ];
            $this->assertEquals($expectedResultOfXor, ArrayUtils::xorArrays($array1, $array2));
        }
    }
