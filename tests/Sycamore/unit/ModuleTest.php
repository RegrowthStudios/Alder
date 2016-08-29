<?php
    namespace SycamoreTest\Sycamore;

    use Sycamore\Module;

    use SycamoreTest\Bootstrap;

    use Zend\EventManager\EventManager;
    use Zend\Http\Response;
    use Zend\Log\Logger;
    use Zend\Mvc\Application;
    use Zend\Mvc\MvcEvent;
    use Zend\ServiceManager\ServiceManager;

    /**
     * Test functionality of Sycamore's Module class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class ModuleTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         *
         * @covers \Sycamore\Module::getConfig
         */
        public function moduleConfigLoadedCorrectlyTest()
        {
            $module = new Module();
            $config = $module->getConfig();

            $this->assertTrue(is_array($config));
            $this->assertTrue(isset($config["service_manager"]["factories"]["Sycamore\Visitor"]));
        }

        /**
         * @test
         *
         * @covers \Sycamore\Module::getAutoloaderConfig
         */
        public function moduleAutoloaderLoadedCorrectlyTest()
        {
            $module = new Module();
            $autoloaderConfig = $module->getAutoloaderConfig();

            $this->assertTrue(is_array($autoloaderConfig));
            $this->assertTrue(isset($autoloaderConfig["Zend\Loader\StandardAutoloader"]["namespaces"]["Sycamore"]));
        }

        /**
         * @test
         *
         * @covers \Sycamore\Module::onBootstrap
         * @covers \Sycamore\Module::prepareServices
         * @covers \Sycamore\Module::createDatabaseCacheService
         * @covers \Sycamore\Module::createSycamoreTableCacheService
         */
        public function bootstrapConstructsServicesTest()
        {
            $eventManager = $this->getMockBuilder(EventManager::class)
                    ->setMethods(["attach"])
                    ->getMock();

            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->setMethods(["get", "setService"])
                    ->getMock();
            $serviceManager->method("get")
                    ->willReturn([ "Sycamore" => [ "cache" => [
                        "namespace" => "sycamore_cache",
                        "timeToLive" => 1800,
                        "adapter" => "filesystem",
                        "plugins" => [
                            "clearExpired" => [
                                "clearingFactor" => 100,
                            ],
                            "ignoreUserAbort" =>  [
                                "exitOnAbort" => false,
                            ],
                            "optimise" => [
                                "optimisingFactor" => 100,
                            ]
                        ]
                    ]]]);
            $serviceManager->expects($this->exactly(2))
                    ->method("setService")
                    ->withConsecutive(
                            ["SycamoreDbCache"],
                            ["SycamoreTableCache"]
                    );

            $application = $this->getMockBuilder(Application::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["getEventManager", "getServiceManager"])
                    ->getMock();
            $application->method("getEventManager")
                    ->willReturn($eventManager);
            $application->method("getServiceManager")
                    ->willReturn($serviceManager);

            $event = $this->getMockBuilder(MvcEvent::class)
                    ->setMethods(["getApplication"])
                    ->getMock();
            $event->method("getApplication")
                    ->willReturn($application);

            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onBootstrap($event);
        }
        
        protected function constructMvcEvent($errorType, $expectedStatusCode, $exception = NULL)
        {
            $response = $this->getMockBuilder(Response::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["setStatusCode", "setContent"])
                    ->getMock();
            $response->expects($this->once())
                    ->method("setStatusCode")
                    ->with($expectedStatusCode);
            
            $mvcEvent = $this->getMockBuilder(MvcEvent::class)
                    ->disableOriginalConstructor()
                    ->getMock();
            $mvcEvent->method("getResponse")
                    ->willReturn($response);
            $mvcEvent->method("getParam")
                    ->willReturn($exception);
            $mvcEvent->method("getError")
                    ->willReturn($errorType);
            
            if ($exception !== NULL) {
                $logger = $this->getMockBuilder(Logger::class)
                        ->setMethods(["crit"])
                        ->getMock();
                $logger->expects($this->once())
                        ->method("crit")
                        ->with($exception);

                $serviceManager = $this->getMockBuilder(ServiceManager::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["get"])
                        ->getMock();
                $serviceManager->method("get")
                        ->willReturn($logger);

                $application = $this->getMockBuilder(Application::class)
                        ->disableOriginalConstructor()
                        ->setMethods(["getServiceManager"])
                        ->getMock();
                $application->method("getServiceManager")
                        ->willReturn($serviceManager);
                
                $mvcEvent->method("getApplication")
                        ->willReturn($application);
            }
            
            return $mvcEvent;
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets404StatusCodeForNoRouteMatch()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_ROUTER_NO_MATCH, 404);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets500StatusCodeForNoControllFound()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_CONTROLLER_NOT_FOUND, 500);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets500StatusCodeForInvalidController()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_CONTROLLER_INVALID, 500);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets500StatusCodeForControllerCannotDispatch()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_CONTROLLER_CANNOT_DISPATCH, 500);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets500StatusCodeForGeneralException()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_EXCEPTION, 500);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorSets500StatusCodeForUnknownError()
        {
            $mvcEvent = $this->constructMvcEvent("TEST-ERROR", 500);
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Module::onDispatchError
         */
        public function onDispatchErrorLogsExceptionIfProvided()
        {
            $mvcEvent = $this->constructMvcEvent(Application::ERROR_EXCEPTION, 500, "Test");
            
            $module = $this->getMockBuilder(Module::class)
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            $module->onDispatchError($mvcEvent);
        }
    }
