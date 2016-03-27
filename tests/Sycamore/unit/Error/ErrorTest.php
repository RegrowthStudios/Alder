<?php
    namespace SycamoreTest\Sycamore\Error;
    
    use Sycamore\Error\Error;
    
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Test functionality of Sycamore's Error class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ErrorTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Error\Error::create
         */
        public function errorMessageCorrectlyFormattedTest()
        {
            $config = [ "Sycamore" => [
                    "test" => 5
                ]
            ];
            $rawErrorMessage = "Error: {test}";
            
            $language = $this->getMockBuilder("language")
                    ->setMockClassName("SycamoreLanguage")
                    ->setMethods(["fetchPhrase"])
                    ->getMock();
            $language->method("fetchPhrase")
                    ->willReturn($rawErrorMessage);
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->setMethods([])
                    ->getMock();
            $serviceManager->method("get")
                    ->will($this->returnCallback(function ($key) use ($config, $language) {
                        if ($key == "Language") {
                            return $language;
                        } else {
                            return $config;
                        }
                    }));
                    
            $returnedErrorMessage = Error::create($serviceManager, "test");
            
            $this->assertEquals("Error: 5", $returnedErrorMessage);
        }
    }
