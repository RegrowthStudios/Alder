<?php
    namespace SycamoreTest\Sycamore\Token;
    
    use Sycamore\Token\Jwt;
    use Sycamore\Token\JwtFactory;
    
    use SycamoreTest\Bootstrap;
    
    use Lcobucci\JWT\Parser;
    
    /**
     * Test functionality of Sycamore's JWT class.
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
         * @covers \Sycamore\Token\Jwt::getPayload
         */
        public function getPayloadReturnsStringTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue(is_string($token->getPayload()));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getPayload
         */
        public function getPayloadReturnsPayloadOfTokenTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $payload = $token->getPayload();
            
            $signature = explode(".", strval($token))[2];
            
            $newToken = (new Parser())->parse(join(".", [$payload, $signature]));
            
            $this->assertSame(strval($token), strval($newToken));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeader
         */
        public function getHeaderReturnsHeaderItemTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $hashFunc = Bootstrap::getServiceManager()->get("Config")["Sycamore"]["security"]["tokenHashAlgorithm"];
            
            $this->assertSame($hashFunc, $token->getHeader("alg"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeader
         */
        public function getHeaderThrowsExceptionIfHeaderNotExistentAndNoDefaultTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->expectException(\OutOfBoundsException::class);
            
            $token->getHeader("test");
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeader
         */
        public function getHeaderReturnsDefaultIfHeaderNotExistentAndDefaultProvidedTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertSame("hello", $token->getHeader("test", "hello"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeaders
         */
        public function getHeadersReturnsArrayTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue(is_array($token->getHeaders()));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeaders
         */
        public function getHeadersReturnsAllHeadersTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $headers = $token->getHeaders();
            
            $this->assertTrue(isset($headers["jti"]) && is_string($headers["jti"]->getValue()));
            $this->assertTrue(isset($headers["alg"]) && is_string($headers["alg"]));
            $this->assertTrue(isset($headers["typ"]) && is_string($headers["typ"]));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getClaim
         */
        public function getClaimReturnsRequestedClaimTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $domain = Bootstrap::getServiceManager()->get("Config")["Sycamore"]["domain"];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $appClaims = $token->getClaim($domain);
            
            $this->assertSame(1, $appClaims->id);
            $this->assertSame("test", $appClaims->username);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getClaim
         */
        public function getClaimThrowsExceptionIfClaimNotExistentAndNoDefaultTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->expectException(\OutOfBoundsException::class);
            
            $token->getClaim("test");
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getHeader
         */
        public function getClaimReturnsDefaultIfClaimNotExistentAndDefaultProvidedTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertSame("hello", $token->getClaim("test", "hello"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getClaims
         */
        public function getClaimsReturnsArrayTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue(is_array($token->getClaims()));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::getClaims
         */
        public function getClaimsReturnsAllClaimsTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $claims = $token->getClaims();
            
            $domain = Bootstrap::getServiceManager()->get("Config")["Sycamore"]["domain"];
            
            $this->assertTrue(isset($claims["iss"]) && is_string($claims["iss"]->getValue()));
            $this->assertTrue(isset($claims["aud"]) && is_array($claims["aud"]->getValue()));
            $this->assertTrue(isset($claims["iat"]) && is_integer($claims["iat"]->getValue()));
            $this->assertTrue(isset($claims["exp"]) && is_integer($claims["exp"]->getValue()));
            $this->assertTrue(isset($claims["nbf"]) && is_integer($claims["nbf"]->getValue()));
            $this->assertTrue(isset($claims["jti"]) && is_string($claims["jti"]->getValue()));
            $this->assertTrue(isset($claims["$domain"]) &&
                    is_integer($claims["$domain"]->getValue()->id) &&
                    is_string($claims["$domain"]->getValue()->username));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::hasClaim
         */
        public function hasClaimReturnsTrueForExistentClaimTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $domain = Bootstrap::getServiceManager()->get("Config")["Sycamore"]["domain"];
            
            $this->assertTrue($token->hasClaim($domain));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::hasClaim
         */
        public function hasClaimReturnsFalseForNonExistentClaimTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertFalse($token->hasClaim("test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::hasHeader
         */
        public function hasHeaderReturnsTrueForExistentHeaderTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertTrue($token->hasHeader("jti"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::hasHeader
         */
        public function hasHeaderReturnsFalseForNonExistentHeaderTest()
        {
            $data = [
                "tokenLifetime" => 36400,
                "applicationPayload" => [
                    "id" => 1,
                    "username" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->assertFalse($token->hasHeader("test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         */
        public function jwtReturnsPreviouslyCalculatedValidityUnlessTokenChangedTest()
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
            
            $reflectionClass = new \ReflectionClass(Jwt::class);
            $stateField = $reflectionClass->getProperty("state");
            $stateField->setAccessible(true);
            
            $this->assertTrue($token->validate($validateData) === Jwt::VALID);
            
            $stateField->setValue($token, 123);
            
            $this->assertSame(123, $token->validate($validateData));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         */
        public function jwtThrowsExceptionIfInvalidSignMethodSpecifiedTest()
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
                "signMethod" => "test",
                "validators" => [
                    "sub" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->expectException(\InvalidArgumentException::class);
            $token->validate($validateData);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         */
        public function jwtThrowsExceptionIfInvalidKeyForAssymetricSignMethodTest()
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
                "signMethod" => "RS256",
                "validators" => [
                    "sub" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->expectException(\InvalidArgumentException::class);
            $token->validate($validateData);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Token\Jwt::validate
         */
        public function jwtThrowsExceptionIfInvalidKeyForAssymetricSignMethodTest()
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
                "signMethod" => "RS256",
                "key" => "test12345",
                "validators" => [
                    "sub" => "test"
                ]
            ];
            
            $token = JwtFactory::create(Bootstrap::getServiceManager(), $data);
            
            $this->expectException(\InvalidArgumentException::class);
            $token->validate($validateData);
        }
    }
