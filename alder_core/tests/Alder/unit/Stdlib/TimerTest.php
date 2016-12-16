<?php
    namespace
    {
        
        $microtimeCalled = false;
    }
    
    namespace Alder\Stdlib
    {
        
        function microtime($bool) {
            global $microtimeCalled;
            if ($bool === true) {
                $microtimeCalled = true;
            }
            
            return \microtime(true);
        }
    }
    
    namespace AlderTest\Alder\Stdlib
    {
        
        use Alder\Stdlib\Timer;
        
        /**
         * Test functionality of Alder's timer class.
         *
         * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
         * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
         * @since     0.1.0
         */
        class TimerTest extends \PHPUnit_Framework_TestCase
        {
            /**
             * Prepares microtimeCalled global variable.
             *
             * @global bool $microtimeCalled
             */
            public function setUp() {
                global $microtimeCalled;
                $microtimeCalled = false;
            }
            
            /**
             * @test
             *
             * @global bool $microtimeCalled
             *
             * @covers \Alder\Stdlib\Timer::start
             */
            public function startingTimerCallsMicrotimeTest() {
                $timer = new Timer();
                $timer->start();
                
                global $microtimeCalled;
                $this->assertTrue($microtimeCalled);
            }
            
            /**
             * @test
             *
             * @global bool $microtimeCalled
             *
             * @covers \Alder\Stdlib\Timer::stop
             */
            public function stoppingTimerCallsMicrotimeTest() {
                global $microtimeCalled;
                
                $timer = new Timer();
                $timer->start();
                $microtimeCalled = false;
                $timer->stop();
                
                $this->assertTrue($microtimeCalled);
            }
            
            /**
             * @test
             *
             * @covers \Alder\Stdlib\Timer::getDuration
             */
            public function durationTimerReturnsIsReasonableTest() {
                $timer = new Timer();
                
                $startTime1 = microtime(true);
                $timer->start();
                $startTime2 = microtime(true);
                
                sleep(0.1);
                
                $stopTime1 = microtime(true);
                $timer->stop();
                $stopTime2 = microtime(true);
                
                $durationMin = $stopTime1 - $startTime2;
                $durationActual = $timer->getDuration();
                $durationMax = $stopTime2 - $startTime1;
                
                $this->assertTrue($durationMin <= $durationActual);
                $this->assertTrue($durationActual <= $durationMax);
            }
        }
    }
