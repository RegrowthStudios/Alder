<?php
    namespace SycamoreTest\Sycamore;
    
    use Sycamore\AbstractRestfulController;
    
    use Zend\Mvc\MvcEvent;
    use Zend\Mvc\Router\RouteMatch;
    use Zend\Http\Headers;
    use Zend\Http\Request;
    use Zend\Http\Response;
    use Zend\Stdlib\ParametersInterface;
    use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
    
    /**
     * Verify has functions related to creation and checking of verification tokens.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class AbstractRestfulControllerTest extends AbstractHttpControllerTestCase
    {
        /**
         * {@inheritdoc}
         */
        protected function setUp()
        {
            
            $this->setApplicationConfig(
                include CONFIG_DIRECTORY . "/sycamore.config.php"
            );
            parent::setUp();
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\AbstractRestfulController::onDispatch
         */
        public function testDeleteActionCalledWithIdentifier()
        {
            $abstractRestfulController = $this->getMockForAbstractClass(AbstractRestfulController::class);
            $abstractRestfulController->method("getIdentifier")
                    ->willReturn(1);
            $abstractRestfulController->method("processBodyContent")
                    ->willReturn("");
            $abstractRestfulController->expects($this->once())
                    ->method("delete")
                    ->with(1);
            
            $response = $this->getMock(Response::class);
            $response->expects($this->once())
                    ->method("setStatusCode")
                    ->with(405);
            
            $reflection = new \ReflectionClass($abstractRestfulController);
            $reflectionProp = $reflection->getProperty("response");
            $reflectionProp->setAccessible(true);
            $reflectionProp->setValue($abstractRestfulController, $response);
            
            $routeMatch = $this->getMock(RouteMatch::class, [], [[]]);
            $routeMatch->method("getParam")
                    ->will($this->returnCallback(function() {
                        $args = func_get_args();
                        var_dump($args);
                        if ($args[0] == "id") {
                            return 1;
                        }
                        return false;
                    }));
            
            $query = $this->getMock(ParametersInterface::class);
            $query->method("get")
                    ->willReturn(false);
            
            $headers = $this->getMock(Headers::class);
            $headers->method("get")
                    ->willReturn(false);
            
            $request = $this->getMock(Request::class);
            $request->method("getMethod")
                    ->willReturn("DELETE");
            $request->method("getQuery")
                    ->willReturn($query);
            $request->method("getHeaders")
                    ->willReturn($headers);
            
            $mvcEvent = $this->getMock(MvcEvent::class);
            $mvcEvent->method("getRouteMatch")
                    ->willReturn($routeMatch);
            $mvcEvent->method("getRequest")
                    ->willReturn($request);
            
            $abstractRestfulController->onDispatch($mvcEvent);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\AbstractRestfulController::onDispatch
         * @covers \Sycamore\AbstractRestfulController::options
         */
        public function testOptionsActionCanBeAccessed()
        {
            $this->dispatch("/api/user", "OPTIONS");
            $this->assertResponseStatusCode(200);
            
            $this->assertModuleName("Sycamore");
            $this->assertControllerName("Sycamore\Controller\API\User\Index");
            $this->assertControllerClass("IndexController");
            $this->assertActionName("options");
        }
    }
    