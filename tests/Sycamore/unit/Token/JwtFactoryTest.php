<?php
    namespace SycamoreTest\Sycamore\Token;
    
    use Sycamore\Token\Jwt;
    use Sycamore\Token\JwtFactory;
    
    use SycamoreTest\Bootstrap;
    
    use Lcobucci\JWT\Parser;
    
    /**
     * Test functionality of Sycamore's JWT factory class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class JwtFactoryTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         * @covers \Sycamore\Token\JwtFactory::create
         */
        public function jwtFactoryReturnsJwtObjectTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "registeredClaims" => [
                    "sub" => "test"
                ],
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue($token instanceof Jwt);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         * @covers \Sycamore\Token\JwtFactory::create
         */
        public function jwtFactoryReturnsValidTokenTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "registeredClaims" => [
                    "sub" => "test"
                ],
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $validateData = [
                "validators" => [
                    "sub" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue($token->validate($validateData) === Jwt::VALID);
        }
    }
