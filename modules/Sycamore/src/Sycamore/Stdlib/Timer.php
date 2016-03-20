<?php
    namespace Sycamore\Stdlib;
    
    /**
     * Simple timer implementation.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Timer
    {
        /**
         * The unix timestamp at the start of the Timer in microseconds.
         *
         * @var float
         */
        protected $startTime = 0;
        
        /**
         * The duration the timer ran for in microseconds.
         *
         * @var float
         */
        protected $duration = 0;
        
        /**
         * Starts the timer.
         */
        public function start()
        {
            $this->startTime = microtime(true);
        }
        
        /**
         * Stops the timer.
         */
        public function stop()
        {
            $this->duration += microtime(true) - $this->startTime;
        }
        
        /**
         * Returns duration the timer ran for.
         * 
         * @return float
         */
        public function getDuration()
        {
            return $this->duration;
        }
    }