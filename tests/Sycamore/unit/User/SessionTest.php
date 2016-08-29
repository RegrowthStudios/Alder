<?php

// A hack to override the global PHP setcookie function.
namespace Sycamore\User {
    
    use Sycamore\Token\Jwt;
    
    use SycamoreTest\Bootstrap;
    
    function setcookie($key, $token, $period, $path, $domain, $httpsOnly, $viaHttpOnly)
    {
        if ($key !== "SLIS" || $domain !== "example.com") {
            return NULL;
        }
        
        $serviceManager = Bootstrap::getServiceManager();
        
        $jwt = new Jwt($serviceManager, $token);
        $applicationPayload = $jwt->getClaims()["example.com"]->getValue();
        
        if (
            $applicationPayload->id !== 1 ||
            $applicationPayload->username !== "test" ||
            $applicationPayload->email !== "test@example.com" ||
            $applicationPayload->superuser !== false
        ) {
            return NULL;
        }
        
        return $token;
    }
}

namespace SycamoreTest\Sycamore\User {
    
    use Sycamore\Cache\TableCache;
    use Sycamore\Db\Table\User;
    use Sycamore\Token\Jwt;
    use Sycamore\User\Session;
    
    use SycamoreTest\Bootstrap;
    
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Test functionality of Sycamore's user session class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class SessionTest extends \PHPUnit_Framework_TestCase
    {
        protected $sessionManager;
        
        public function setUp()
        {
            $config = [
                "Sycamore" => [
                    "domain" => "example.com",
                    "security" => [
                        "sessionLength" => 36400,
                        "sessionLengthExtended" => 364000,
                        "sessionsOverHttpsOnly" => false,
                        "accessCookiesViaHttpOnly" => false,
                        "tokenPrivateKey" => "test",
                        "tokenPublicKey" => "test",
                        "tokenHashAlgorithm" => "HS256",
                        "verifyTokenLifetime" => 21600
                    ],
                ]
            ];
            
            $user = new \stdClass();
            $user->id = 1;
            $user->username = "test";
            $user->email = "test@example.com";
            $user->superUser = false;
            
            $table = $this->getMockBuilder(User::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["getByEmail", "getByUsername"])
                    ->getMock();
            $table->method("getByEmail")
                    ->willReturn($user);
            $table->method("getByUsername")
                    ->willReturn($user);
            
            $tableCache = $this->getMockBuilder(TableCache::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchTable"])
                    ->getMock();
            $tableCache->method("fetchTable")
                    ->willReturn($table);
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($tableCache, $config) {
                        if ($key == "SycamoreTableCache") {
                            return $tableCache;
                        } else if ($key == "Config") {
                            return $config;
                        } else {
                            return NULL;
                        }
                    }));
            
            $sessionManager = $this->getMockBuilder(Session::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $this->sessionManager = $sessionManager;
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::__construct
         */
        public function sessionClassCorrectlyConstructsTest()
        {
            $sessionManager = new Session(Bootstrap::getServiceManager());
            $this->assertTrue($sessionManager instanceof Session);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::create
         */
        public function sessionCreationSetsCookieCorrectlyTest()
        {            
            $this->assertTrue(is_string($this->sessionManager->create("test", false)));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::create
         */
        public function sessionCreationReturnsFalseIfInvalidUserTest()
        {
            $config = [
                "Sycamore" => [
                    "domain" => "example.com",
                    "security" => [
                        "sessionLength" => 36400,
                        "sessionLengthExtended" => 364000,
                        "sessionsOverHttpsOnly" => false,
                        "accessCookiesViaHttpOnly" => false,
                        "tokenPrivateKey" => "test",
                        "tokenPublicKey" => "test",
                        "tokenHashAlgorithm" => "HS256",
                        "verifyTokenLifetime" => 21600
                    ],
                ]
            ];
            
            $table = $this->getMockBuilder(User::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["getByEmail", "getByUsername"])
                    ->getMock();
            $table->method("getByEmail")
                    ->willReturn(false);
            $table->method("getByUsername")
                    ->willReturn(false);
            
            $tableCache = $this->getMockBuilder(TableCache::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchTable"])
                    ->getMock();
            $tableCache->method("fetchTable")
                    ->willReturn($table);
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($tableCache, $config) {
                        if ($key == "SycamoreTableCache") {
                            return $tableCache;
                        } else if ($key == "Config") {
                            return $config;
                        } else {
                            return NULL;
                        }
                    }));
            
            $sessionManager = $this->getMockBuilder(Session::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $this->assertFalse($sessionManager->create("test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::create
         */
        public function sessionCreationSetsCookieCorrectlyForExtendedSessionTest()
        {            
            $this->assertTrue(is_string($this->sessionManager->create("test", true)));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::create
         * @covers \Sycamore\User\Session::acquire
         */
        public function sessionAcquireReturnsTrueAndProvidesAppClaimsForValidEmailedSessionTest()
        {
            $token = $this->sessionManager->create("test", false);
            
            $_COOKIE["SLIS"] = $token;
            
            $tokenClaims = [];
            $result = $this->sessionManager->acquire($tokenClaims);
            
            $this->assertTrue($result === Jwt::VALID);
            $this->assertTrue(
                $tokenClaims->id === 1 &&
                $tokenClaims->username === "test" &&
                $tokenClaims->email === "test@example.com" &&
                $tokenClaims->superuser === false
            );
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::create
         * @covers \Sycamore\User\Session::acquire
         */
        public function sessionAcquireReturnsTrueAdProvidesAppClaimsForValidUsernamedSessionTest()
        {
            $token = $this->sessionManager->create("testm9@yahoo.com", false);
            
            $_COOKIE["SLIS"] = $token;
            
            $tokenClaims = [];
            $result = $this->sessionManager->acquire($tokenClaims);
            
            $this->assertTrue($result === Jwt::VALID);
            $this->assertTrue(
                $tokenClaims->id === 1 &&
                $tokenClaims->username === "test" &&
                $tokenClaims->email === "test@example.com" &&
                $tokenClaims->superuser === false
            );
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::acquire
         */
        public function sessionAcquireReturnsFalseAndNoAppClaimsForInvalidSessionTest()
        {
            $token = $this->sessionManager->create("test", false);
            $token .= "t";
            
            $_COOKIE["SLIS"] = $token;
            
            $tokenClaims = [];
            $result = $this->sessionManager->acquire($tokenClaims);
            
            $this->assertFalse($result === Jwt::VALID);
            $this->assertEmpty($tokenClaims);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::acquire
         */
        public function sessionAcquireReturnsZeroForUnsetCookieTest()
        {
            unset($_COOKIE["SLIS"]);
            
            $tokenClaims = [];
            $this->assertSame(0, $this->sessionManager->acquire($tokenClaims));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Session::acquire
         */
        public function sessionAcquireReturnsZeroForFalsyCookieTest()
        {
            $_COOKIE["SLIS"] = false;
            
            $tokenClaims = [];
            $this->assertSame(0, $this->sessionManager->acquire($tokenClaims));
        }
    }
}