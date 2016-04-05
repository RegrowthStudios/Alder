<?php
    namespace SycamoreTest\Sycamore;

    use Sycamore\Module;
    use Sycamore\Stdlib\ArrayUtils;
    use Sycamore\Visitor;

    use SycamoreTest\Bootstrap;

    use Zend\EventManager\EventManager;
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
    }
