<?php
    namespace SycamoreTest\Sycamore;
    
    use Sycamore\AbstractRestfulController;
    
    use Zend\Mvc\MvcEvent;
    use Zend\Mvc\Router\RouteMatch;
    use Zend\Http\Request;
    use Zend\Http\Response;
    use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
    
    /**
     * Test functionality of the Sycamore AbstractRestfulController class.
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
         * Prepares the controller and mvc event for testing the onDispatch function of \Sycamore\AbstractRestfulController. 
         */
        public function correctRoutingTestProvider()
        {
            $methodDefaults = [
                "httpMethod" => "fake",
                "expectedAction" => NULL,
                "id" => false,
                "bodyContent" => "",
                "routeMatchGetParam" => false,
                "queryGetParam" => false,
                "expectedResponseCode" => NULL
            ];
            $methods = [
                [
                    "key" => "deleteSingular",
                    "httpMethod" => "delete",
                    "expectedAction" => "delete",
                    "id" => 1,
                ],
                [
                    "key" => "deleteMultiple",
                    "httpMethod" => "delete",
                    "expectedAction" => "deleteList",
                ],
                [
                    "key" => "getSingular",
                    "httpMethod" => "get",
                    "expectedAction" => "get",
                    "id" => 1,
                ],
                [
                    "key" => "getMultiple",
                    "httpMethod" => "get",
                    "expectedAction" => "getList",
                ],
                [
                    "key" => "getHead",
                    "httpMethod" => "head",
                    "expectedAction" => "head",
                ],
                [
                    "key" => "getOptions",
                    "httpMethod" => "options",
                    "expectedAction" => "options",
                ],
                [
                    "key" => "patchSingular",
                    "httpMethod" => "patch",
                    "expectedAction" => "patch",
                    "id" => 1,
                ],
                [
                    "key" => "patchMultiple",
                    "httpMethod" => "patch",
                    "expectedAction" => "patchList",
                ],
                [
                    "key" => "createResource",
                    "httpMethod" => "post",
                    "expectedAction" => "processPostData",
                ],
                [
                    "key" => "replaceResourceSingular",
                    "httpMethod" => "put",
                    "expectedAction" => "replace",
                    "id" => 1,
                ],
                [
                    "key" => "replaceResourceMultiple",
                    "httpMethod" => "put",
                    "expectedAction" => "replaceList",
                ],
                [
                    "key" => "noMethodSpecified",
                    "expectedResponseCode" => 405,
                ],
                [
                    "key" => "testCustomAction",
                    "expectedAction" => "testAction",
                    "routeMatchGetParam" => "test",
                ],
            ];
            
            // Iterate over the different methods and yield the constructed mock abstract controller.
            foreach ($methods as $method) {
                // Merge in defaults.
                $method = array_merge($methodDefaults, $method);
                
                $abstractRestfulController = $this->getMockBuilder(AbstractRestfulController::class)
                        ->setMethods(($method["expectedAction"] !== NULL) ? ["getIdentifier", "processBodyContent", $method["expectedAction"]] : ["getIdentifier", "processBodyContent"])
                        ->getMockForAbstractClass();
                $abstractRestfulController->method("getIdentifier")
                        ->willReturn($method["id"]);
                $abstractRestfulController->method("processBodyContent")
                        ->willReturn($method["bodyContent"]);
                if ($method["expectedAction"] !== NULL) {
                    if ($method["id"]) {
                        $abstractRestfulController->expects($this->once())
                                ->method($method["expectedAction"])
                                ->with($method["id"]);
                    } else {
                        $abstractRestfulController->expects($this->once())
                                ->method($method["expectedAction"]);
                    }
                }
                
                $routeMatch = $this->getMockBuilder(RouteMatch::class)
                        ->setMethods(["getParam"])
                        ->setConstructorArgs([[]])
                        ->getMock();
                $routeMatch->method("getParam")
                        ->willReturn($method["routeMatchGetParam"]);

                // Don't override any functions - we want Response to act normally.
                $response = $this->getMock(Response::class, NULL);
                
                $mvcEvent = $this->getMockBuilder(MvcEvent::class)
                        ->setMethods(["getRouteMatch", "getRequest", "getResponse"])
                        ->getMock();
                $mvcEvent->method("getRouteMatch")
                        ->willReturn($routeMatch);
                $mvcEvent->method("getResponse")
                        ->willReturn($response);
                
                $request = $this->getMockBuilder(Request::class)
                        ->setMethods(["getMethod"])
                        ->getMock();

                if ($method["httpMethod"] !== NULL) {
                    $request->method("getMethod")
                            ->willReturn($method["httpMethod"]);
                }
                
                $mvcEvent->method("getRequest")
                        ->willReturn($request);

                yield $method["key"] => [$abstractRestfulController, $mvcEvent, $method["expectedResponseCode"]];
            }
        }
        
        /**
         * @test
         * 
         * @dataProvider correctRoutingTestProvider
         * 
         * @covers \Sycamore\AbstractRestfulController::onDispatch
         */
        public function testRequestCorrectlyRouted($abstractRestfulController, $mvcEvent, $expectedStatusCode)
        {
            $result = $abstractRestfulController->onDispatch($mvcEvent);
            if ($expectedStatusCode !== NULL) {
                $this->assertEquals($expectedStatusCode, $result->getStatusCode());
            }
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
    