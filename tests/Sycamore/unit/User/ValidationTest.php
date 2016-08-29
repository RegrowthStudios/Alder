<?php

    namespace SycamoreTest\Sycamore\User;
    
    use Sycamore\User\Validation;
    
    use SycamoreTest\Bootstrap;
    
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Test functionality of Sycamore's user validation class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ValidationTest extends \PHPUnit_Framework_TestCase
    {
        protected $validationManagerUnique;
        protected $validationManagerNotUnique;
        
        public function setUp()
        {
            $config = [
                "Sycamore" => [
                    "domain" => "example.com",
                    "security" => [
                        "password" => [
                            "strictness" => "normal",
                            "hashingStrength" => 11,
                            "minimumLength" => 8,
                            "maximumLength" => 48
                        ]
                    ],
                    "username" => [
                        "minimumLength" => 2,
                        "maximumLength" => 16
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            { /* Set up validation object where all usernames and emails are unique. */
                $tableUnique = $this->getMockBuilder(User::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["isUsernameUnique", "isEmailUnique"])
                        ->getMock();
                $tableUnique->method("isUsernameUnique")
                        ->willReturn(true);
                $tableUnique->method("isEmailUnique")
                        ->willReturn(true);

                $tableCacheUnique = $this->getMockBuilder(TableCache::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["fetchTable"])
                        ->getMock();
                $tableCacheUnique->method("fetchTable")
                        ->willReturn($tableUnique);

                $serviceManagerUnique = $this->getMockBuilder(ServiceManager::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["get"])
                        ->getMock();
                $serviceManagerUnique->method("get")
                        ->will($this->returnCallback(function($key) use ($tableCacheUnique, $config, $language) {
                            if ($key == "SycamoreTableCache") {
                                return $tableCacheUnique;
                            } else if ($key == "Config") {
                                return $config;
                            } else if ($key == "Language") {
                                return $language;
                            } else {
                                return NULL;
                            }
                        }));

                $validationManagerUnique = $this->getMockBuilder(Validation::class)
                        ->setConstructorArgs([&$serviceManagerUnique])
                        ->setMethods(NULL)
                        ->getMock();

                $this->validationManagerUnique = $validationManagerUnique;
            }
            
            { /* Set up validation object where all usernames and emails are NOT unique. */
                $tableNotUnique = $this->getMockBuilder(User::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["isUsernameUnique", "isEmailUnique"])
                        ->getMock();
                $tableNotUnique->method("isUsernameUnique")
                        ->willReturn(false);
                $tableNotUnique->method("isEmailUnique")
                        ->willReturn(false);

                $tableCacheNotUnique = $this->getMockBuilder(TableCache::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["fetchTable"])
                        ->getMock();
                $tableCacheNotUnique->method("fetchTable")
                        ->willReturn($tableNotUnique);

                $serviceManagerNotUnique = $this->getMockBuilder(ServiceManager::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["get"])
                        ->getMock();
                $serviceManagerNotUnique->method("get")
                        ->will($this->returnCallback(function($key) use ($tableCacheNotUnique, $config, $language) {
                            if ($key == "SycamoreTableCache") {
                                return $tableCacheNotUnique;
                            } else if ($key == "Config") {
                                return $config;
                            } else if ($key == "Language") {
                                return $language;
                            } else {
                                return NULL;
                            }
                        }));

                $validationManagerNotUnique = $this->getMockBuilder(Validation::class)
                        ->setConstructorArgs([&$serviceManagerNotUnique])
                        ->setMethods(NULL)
                        ->getMock();

                $this->validationManagerNotUnique = $validationManagerNotUnique;
            }
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::__construct
         */
        public function validationClassCorrectlyConstructsTest()
        {
            $validationManager = new Validation(Bootstrap::getServiceManager());
            $this->assertTrue($validationManager instanceof Validation);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateUsername
         * @covers \Sycamore\User\Validation::isUsername
         * @covers \Sycamore\User\Validation::isUniqueUsername
         */
        public function validateUsernameReturnsTrueForValidAndUniqueUsername()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->validateUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateUsername
         * @covers \Sycamore\User\Validation::isUsername
         * @covers \Sycamore\User\Validation::isUniqueUsername
         */
        public function validateUsernameReturnsFalseForInvalidFormattedUsername()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->validateUsername("*=", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateUsername
         * @covers \Sycamore\User\Validation::isUsername
         * @covers \Sycamore\User\Validation::isUniqueUsername
         */
        public function validateUsernameReturnsFalseForNonUniqueUsername()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->validateUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateEmail
         * @covers \Sycamore\User\Validation::isEmail
         * @covers \Sycamore\User\Validation::isUniqueEmail
         */
        public function validateEmailReturnsTrueForValidAndUniqueEmail()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->validateEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateEmail
         * @covers \Sycamore\User\Validation::isEmail
         * @covers \Sycamore\User\Validation::isUniqueEmail
         */
        public function validateEmailReturnsFalseForInvalidFormattedEmail()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->validateEmail("test", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::validateEmail
         * @covers \Sycamore\User\Validation::isEmail
         * @covers \Sycamore\User\Validation::isUniqueEmail
         */
        public function validateEmailReturnsFalseForNonUniqueEmail()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->validateEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckTriggersWarningIfMinimumPasswordLengthGreaterThanMaximum()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 32,
                            "maximumLength" => 3,
                            "strictness" => PASSWORD_STRICTNESS_NORMAL
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordRightLength = "password1";
            
            $this->setExpectedException("PHPUnit_Framework_Error");
            
            $errors = [];
            $validationManager->passwordStrengthCheck($passwordRightLength, $errors);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckReturnsFalseForTooShortPassword()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 3,
                            "maximumLength" => 32,
                            "strictness" => PASSWORD_STRICTNESS_NORMAL
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordRightLength = "password1";
            $passwordWrongLength = "p";
            
            $errors = [];
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordRightLength, $errors));
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordWrongLength, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckReturnsFalseForTooLongPassword()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 3,
                            "maximumLength" => 10,
                            "strictness" => PASSWORD_STRICTNESS_NORMAL
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordRightLength = "password1";
            $passwordWrongLength = "password12345";
            
            $errors = [];
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordRightLength, $errors));
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordWrongLength, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckRequiresLetterAndNumberForNormalStrictness()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 3,
                            "maximumLength" => 32,
                            "strictness" => PASSWORD_STRICTNESS_NORMAL
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordStupid1 = "abcde";
            $passwordStupid2 = "12345";
            $passwordWeak = "password1";
            $passwordNormal = "Taasdw2";
            $passwordStrong = "Tas7AS*h$";
            
            $errors = [];
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordStupid1, $errors));
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordStupid2, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordWeak, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordNormal, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordStrong, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckRequiresLetterNumberAndCapitalLetterForHighStrictness()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 3,
                            "maximumLength" => 32,
                            "strictness" => PASSWORD_STRICTNESS_HIGH
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordWeak = "password1";
            $passwordNormal = "Taasdw2";
            $passwordStrong = "Tas7AS*h$";
            
            $errors = [];
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordWeak, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordNormal, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordStrong, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::passwordStrengthCheck
         */
        public function passwordStrengthCheckRequiresLetterNumberCapitalLetterAndSymbolForStrictStrictness()
        {
            $config = [
                "Sycamore" => [
                    "security" => [
                        "password" => [
                            "minimumLength" => 3,
                            "maximumLength" => 32,
                            "strictness" => PASSWORD_STRICTNESS_STRICT
                        ]
                    ]
                ]
            ];
            
            $language = $this->getMockBuilder("SycamoreLanguage")
                    ->setMockClassName("SycamoreLanguage")
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn("An error occurred.");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function($key) use ($config, $language) {
                        if ($key == "Config") {
                            return $config;
                        } else if ($key == "Language") {
                            return $language;
                        } else {
                            return NULL;
                        }
                    }));
                    
            $validationManager = $this->getMockBuilder(Validation::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->setMethods(NULL)
                    ->getMock();
            
            $passwordWeak = "password1";
            $passwordNormal = "Taasdw2";
            $passwordStrong = "Tas7AS*h$";
            
            $errors = [];
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordWeak, $errors));
            $this->assertFalse($validationManager->passwordStrengthCheck($passwordNormal, $errors));
            $this->assertTrue($validationManager->passwordStrengthCheck($passwordStrong, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isEmail
         */
        public function isEmailIdentifiesValidEmails()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->isEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isEmail
         */
        public function isEmailIdentifiesInvalidEmails()
        {
            $invalidEmail1 = "test";
            $invalidEmail2 = "test@";
            $invalidEmail3 = "test@example";
            
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->isEmail($invalidEmail1, $errors));
            $this->assertFalse($this->validationManagerUnique->isEmail($invalidEmail2, $errors));
            $this->assertFalse($this->validationManagerUnique->isEmail($invalidEmail3, $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUniqueEmail
         */
        public function isUniqueEmailReturnsTrueForUniqueEmail()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->isUniqueEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUniqueEmail
         */
        public function isUniqueEmailReturnsFalseForNonUniqueEmail()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->isUniqueEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUsername
         */
        public function isUsernameIdentifiesValidUsernames()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->isUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUsername
         */
        public function isUsernameIdentifiesTooLongUsernames()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->isUsername("testtesttesttesttesttestttesesttest", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUsername
         */
        public function isUsernameIdentifiesTooShortUsernames()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->isUsername("t", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUsername
         */
        public function isUsernameIdentifiesInvalidCharacterUsernames()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->isUsername("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUniqueUsername
         */
        public function isUniqueUsernameReturnsTrueForUniqueUsername()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->isUniqueUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User\Validation::isUniqueUsername
         */
        public function isUniqueUsernameReturnsFalseForNonUniqueUsername()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->isUniqueUsername("testusername", $errors));
        }
    }
