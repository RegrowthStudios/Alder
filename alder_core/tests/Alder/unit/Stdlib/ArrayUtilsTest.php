<?php
    namespace AlderTest\Alder\Stdlib;
    
    use Alder\Stdlib\ArrayUtils;
    
    use AlderTest\SimpleArrayAccessObject;
    use AlderTest\SimpleTraversableObject;
        
    /**
     * Test functionality of the Array utility functions class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\ArrayUtils::inArrayRecursive
         */
        public function inArrayRecursiveReturnsTrueForExpectedItemInArbitraryArrayDepthTest()
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
         * @covers \Alder\Stdlib\ArrayUtils::inArrayRecursive
         */
        public function inArrayRecursiveReturnsFalseForNoExpectedItemInArbitraryArrayDepthTest()
        {
            $array1 = [ "test" ];
            $array2 = [ $array1 ];
            $array3 = [ $array2 ];
            $array4 = [ $array3 ];
            
            $this->assertFalse(ArrayUtils::inArrayRecursive("test1", $array1));
            $this->assertFalse(ArrayUtils::inArrayRecursive("test1", $array2));
            $this->assertFalse(ArrayUtils::inArrayRecursive("test1", $array3));
            $this->assertFalse(ArrayUtils::inArrayRecursive("test1", $array4));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\ArrayUtils::arrayKeyExistsRecursive
         */
        public function arrayKeyExistsRecursiveReturnsTrueForExpectedKeyInArbitraryArrayDepthTest()
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
         * @covers \Alder\Stdlib\ArrayUtils::arrayKeyExistsRecursive
         */
        public function arrayKeyExistsRecursiveReturnsFalseForNoExpectedKeyInArbitraryArrayDepthTest()
        {
            $array1 = [ "test" => 1 ];
            $array2 = [ $array1 ];
            $array3 = [ $array2 ];
            $array4 = [ $array3 ];
            
            $this->assertFalse(ArrayUtils::arrayKeyExistsRecursive("test1", $array1));
            $this->assertFalse(ArrayUtils::arrayKeyExistsRecursive("test1", $array2));
            $this->assertFalse(ArrayUtils::arrayKeyExistsRecursive("test1", $array3));
            $this->assertFalse(ArrayUtils::arrayKeyExistsRecursive("test1", $array4));
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\ArrayUtils::recursiveAsort
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
         * @covers \Alder\Stdlib\ArrayUtils::recursiveAsort
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
         * @covers \Alder\Stdlib\ArrayUtils::recursiveKsort
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
         * @covers \Alder\Stdlib\ArrayUtils::recursiveKsort
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
         * @covers \Alder\Stdlib\ArrayUtils::validateArrayLike
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
         * @covers \Alder\Stdlib\ArrayUtils::validateArrayLike
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
         * @covers \Alder\Stdlib\ArrayUtils::validateArrayLike
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
         * @covers \Alder\Stdlib\ArrayUtils::validateArrayLike
         */
        public function validateArrayLikeDoesNotValidateInvalidObjectsTest()
        {
            $this->expectException(\InvalidArgumentException::class);
            
            ArrayUtils::validateArrayLike(new \stdClass());
        }
        
        /**
         * @test
         * 
         * @covers \Alder\Stdlib\ArrayUtils::validateArrayLike
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
         * @covers \Alder\Stdlib\ArrayUtils::xorArrays
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
         * @covers \Alder\Stdlib\ArrayUtils::xorArrays
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
