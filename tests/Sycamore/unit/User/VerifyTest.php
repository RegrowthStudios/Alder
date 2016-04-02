<?php

    namespace SycamoreTest\Sycamore\User;
    
    use Sycamore\Token\Jwt;
    
    use SycamoreTest\Bootstrap;
    
    /**
     * Test functionality of Sycamore's user verification class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class VerifyTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\User\Verify::create
         */
        public function createReturnsInstanceOfJwtOnSuccessTest()
        {
            $verifyManager = Bootstrap::getServiceManager()->get("Sycamore\User\Verify");
            
            $this->assertTrue($verifyManager->create(1, ["test" => "hello"]) instanceof Jwt);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Verify::create
         */
        public function createReturnsTokenWithIntendedItemsTest()
        {
            $verifyManager = Bootstrap::getServiceManager()->get("Sycamore\User\Verify");
            
            $token = $verifyManager->create(1, ["test" => "hello"]);
            
            $claim = $token->getClaims()["example.com"]->getValue();
            
            $this->assertTrue($claim->test === "hello");
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Verify::verfiy
         */
        public function validTokenCorrectlyVerifiedTest()
        {
            $verifyManager = Bootstrap::getServiceManager()->get("Sycamore\User\Verify");
            
            $token = $verifyManager->create(1, ["test" => "hello"]);
            
            $result = $verifyManager->verify(1, strval($token), ["test" => "hello"]);
            
            $this->assertNotFalse($result);
            $this->assertTrue(isset($result->test));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Verify::verify
         */
        public function invalidTokenFailsVerificationTest()
        {
            $verifyManager = Bootstrap::getServiceManager()->get("Sycamore\User\Verify");
            
            $token = $verifyManager->create(1, ["test" => "hello"]);
            
            $parts = explode(".",strval($token));
            $parts[1] = "t" . $parts[1]; 
            
            $this->assertFalse($verifyManager->verify(1, join(".", $parts), ["test" => "hello"]));
        }
    }
