<?php
    namespace SycamoreTest\Sycamore\User;
    
    use Sycamore\User\Security;
    
    use SycamoreTest\Bootstrap;
    
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Test functionality of Sycamore's user security class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class SecurityTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::__construct
         */
        public function securityClassCorrectlyConstructsTest()
        {
            $securityManager = new Security(Bootstrap::getServiceManager());
            $this->assertTrue($securityManager instanceof Security);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::hashPassword
         */
        public function hashPasswordReturnsHashedPasswordTest()
        {
            $password = "password";
            
            $hash = Bootstrap::getServiceManager()->get("Sycamore\User\Security")->hashPassword($password);
            
            $this->assertTrue(password_verify($password, $hash));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::verifyPassword
         */
        public function hashedPasswordCanBeVerfiedByVerifyPasswordTest()
        {
            $password = "password";
            
            $hash = Bootstrap::getServiceManager()->get("Sycamore\User\Security")->hashPassword($password);
            
            $this->assertTrue(Bootstrap::getServiceManager()->get("Sycamore\User\Security")->verifyPassword($password, $hash));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::verifyPassword
         */
        public function verifyPasswordReturnsFalseForIncorrectPasswordTest()
        {
            $password = "password";
            
            $hash = Bootstrap::getServiceManager()->get("Sycamore\User\Security")->hashPassword($password);
            
            $this->assertFalse(Bootstrap::getServiceManager()->get("Sycamore\User\Security")->verifyPassword($password . "test", $hash));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::passwordNeedsRehash
         */
        public function passwordNeedsRehashReturnsFalseForUpToDatePasswordTest()
        {
            $password = "password";
            
            $hash = Bootstrap::getServiceManager()->get("Sycamore\User\Security")->hashPassword($password);
            
            $this->assertFalse(Bootstrap::getServiceManager()->get("Sycamore\User\Security")->passwordNeedsRehash($hash));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Security::passwordNeedsRehash
         */
        public function passwordNeedsRehashReturnsTrueForOutOfDatePasswordTest()
        {
            $password = "password";
            
            $hash = Bootstrap::getServiceManager()->get("Sycamore\User\Security")->hashPassword($password);
            
            $sm = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $sm->method("get")
                    ->willReturn([
                        "Sycamore" => [
                            "security" => [
                                "password" => [
                                    "hashingStrength" => 1 + Bootstrap::getServiceManager()->get("Config")["Sycamore"]["security"]["password"]["hashingStrength"]
                                ]
                            ]
                        ]
                    ]);
            $userSecurity = new Security($sm);
            
            $this->assertTrue($userSecurity->passwordNeedsRehash($hash));
        }
    }
