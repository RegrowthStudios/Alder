<?php
    namespace SycamoreTest\Sycamore;
    
    use Sycamore\Visitor;
    use Sycamore\User\Session;
    
    use SycamoreTest\Bootstrap;
    
    use Zend\ServiceManager\ServiceManager;
    
    /**
     * Test functionality of Sycamore's Visitor class.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class VisitorTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::__construct
         */
        public function visitorObjectCreationShouldPrepareVisitor()
        {
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->disableOriginalConstructor()
                    ->disableArgumentCloning()
                    ->setMethods(["prepareVisitor"])
                    ->getMock();
            $visitor->expects($this->once())
                    ->method("prepareVisitor");
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->disableOriginalConstructor()
                    ->getMock();
            $serviceManagerRfr = &$serviceManager;
            
            $reflectedVisitor = new \ReflectionClass(Visitor::class);
            $constructor = $reflectedVisitor->getConstructor();
            $constructor->invoke($visitor, $serviceManagerRfr);
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::get
         */
        public function fetchRequestsForNonFetchedItemsResultsInFetchCallTest()
        {
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchTest"])
                    ->getMock();
            $visitor->expects($this->once())
                    ->method("fetchTest")
                    ->willReturn("test");
            
            $this->assertEquals("test", $visitor->get("test"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::get
         */
        public function fetchRequestsForNonExistentItemsResultsInExceptionTest()
        {
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["fetchTest"])
                    ->getMock();
            $visitor->expects($this->once())
                    ->method("fetchTest")
                    ->willReturn(NULL);
            
            $this->expectException(\InvalidArgumentException::class);
            
            $visitor->get("test");
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::prepareVisitor
         * @covers \Sycamore\Visitor::isLoggedIn
         */
        public function visitorLoggedInShouldReturnIsLoggedInTrueTest()
        {
            $userSession = $this->getMockBuilder(Session::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["acquire"])
                    ->getMock();
            $userSession->method("acquire")
                    ->willReturn(true);
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->willReturn($userSession);
            
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            
            $this->assertTrue($visitor->isLoggedIn());
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::prepareVisitor
         * @covers \Sycamore\Visitor::get
         */
        public function visitorLoggedInShouldReturnInformationRegardingUserTest()
        {
            $id = 1;
            
            $userSession = $this->getMockBuilder(Session::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["acquire"])
                    ->getMock();
            $userSession->method("acquire")
                    ->will($this->returnCallback(function(& $tokenClaimDump) use ($id) {
                        $tokenClaimDump = [
                            "id" => $id,
                        ];
                        return true;
                    }));
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->willReturn($userSession);
            
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            
            $this->assertEquals($id, $visitor->get("id"));
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::prepareVisitor
         * @covers \Sycamore\Visitor::isLoggedIn
         */
        public function visitorNotLoggedInShouldReturnIsLoggedInFalseTest()
        {
            $userSession = $this->getMockBuilder(Session::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["acquire"])
                    ->getMock();
            $userSession->method("acquire")
                    ->willReturn(false);
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->willReturn($userSession);
            
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            
            $this->assertFalse($visitor->isLoggedIn());
        }
        
        /**
         * @test
         * 
         * @covers \Sycamore\Visitor::prepareVisitor
         * @covers \Sycamore\Visitor::get
         * 
         * @expectedException InvalidArgumentException
         */
        public function visitorNotLoggedInShouldNotReturnInformationRegardingUserTest()
        {
            $id = 1;
            
            $userSession = $this->getMockBuilder(Session::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["acquire"])
                    ->getMock();
            $userSession->method("acquire")
                    ->will($this->returnCallback(function(& $tokenClaimDump) use ($id) {
                        $tokenClaimDump = [
                            "id" => $id,
                        ];
                        return false;
                    }));
            
            $serviceManager = $this->getMockBuilder(ServiceManager::class)
                    ->setMethods(["get"])
                    ->getMock();
            $serviceManager->method("get")
                    ->willReturn($userSession);
            
            $visitor = $this->getMockBuilder(Visitor::class)
                    ->setConstructorArgs([&$serviceManager])
                    ->disableArgumentCloning()
                    ->setMethods(NULL)
                    ->getMock();
            
            $visitor->get("id");
        }
    }
