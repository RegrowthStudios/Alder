<?php
    namespace SycamoreTest\Sycamore\Token;
    
    use Sycamore\Token\Jwt;
    use Sycamore\Token\JwtFactory;
    
    use SycamoreTest\Bootstrap;
    
    /**
     * Test functionality of Sycamore's JWT class and its respective factory class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class JwtTest extends \PHPUnit_Framework_TestCase
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
            
            $this->assertTrue($token->validate($validateData) == Jwt::VALID);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::__construct
         * @covers \Sycamore\Token\Jwt::__toString
         */
        public function jwtTokenConstructedFromTokenStringCanBeConvertedToOriginalStringTest()
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
            
            $tokenStr = strval($token);
            
            $newToken = new Jwt(Bootstrap::getServiceManager(), $token);
            
            $this->assertSame($tokenStr, strval($newToken));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::setToken
         * @covers \Sycamore\Token\Jwt::__toString
         */
        public function jwtTokenSetViaSetTokenCanBeConvertedToOriginalTokenStringTest()
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
                        
            $newToken = new Jwt(Bootstrap::getServiceManager());
            $newToken->setToken(strval($token));
            
            $this->assertSame(strval($token), strval($newToken));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getClaims
         */
        public function jwtTokenReturnsClaimsItWasSetWithTest()
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
                        
            $appClaims = $token->getClaims()["example.com"]->getValue();
            
            $this->assertSame(1, $appClaims->id);
            $this->assertSame("test", $appClaims->username);
        }
    }
