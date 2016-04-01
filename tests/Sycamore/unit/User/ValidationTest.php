<?php

    namespace SycamoreTest\Sycamore\User;
    
    use Sycamore\User\Validation;
    
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
                        "minimumLength" => 1,
                        "maximumLength" => 32
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
         * @covers \Sycamore\User::validateUsername
         * @covers \Sycamore\User::isUsername
         * @covers \Sycamore\User::isUniqueUsername
         */
        public function validateUsernameReturnsTrueForValidAndUniqueUsername()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->validateUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User::validateUsername
         * @covers \Sycamore\User::isUsername
         * @covers \Sycamore\User::isUniqueUsername
         */
        public function validateUsernameReturnsFalseForInvalidFormattedUsername()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->validateUsername("*=", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User::validateUsername
         * @covers \Sycamore\User::isUsername
         * @covers \Sycamore\User::isUniqueUsername
         */
        public function validateUsernameReturnsFalseForNonUniqueUsername()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->validateUsername("testusername", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User::validateEmail
         * @covers \Sycamore\User::isEmail
         * @covers \Sycamore\User::isUniqueEmail
         */
        public function validateEmailReturnsTrueForValidAndUniqueEmail()
        {
            $errors = [];
            $this->assertTrue($this->validationManagerUnique->validateEmail("test@example.com", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User::validateEmail
         * @covers \Sycamore\User::isEmail
         * @covers \Sycamore\User::isUniqueEmail
         */
        public function validateEmailReturnsFalseForInvalidFormattedEmail()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerUnique->validateEmail("test", $errors));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\User::validateEmail
         * @covers \Sycamore\User::isEmail
         * @covers \Sycamore\User::isUniqueEmail
         */
        public function validateEmailReturnsFalseForNonUniqueEmail()
        {
            $errors = [];
            $this->assertFalse($this->validationManagerNotUnique->validateEmail("test@example.com", $errors));
        }
        
        // TODO(Matthew): Add functions to test individual isEmail/isUsername etc. funcs + passwordStrengthCheck.
    }
