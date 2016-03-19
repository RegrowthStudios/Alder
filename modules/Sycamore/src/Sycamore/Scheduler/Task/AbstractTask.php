<?php

/**
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt GNU General Public License 3.0
 */

    namespace Sycamore\Scheduler\Task;
    
    use Sycamore\Scheduler\Task\TaskInterface;
    
    /**
     * Abstract task class used as a base for all task objects.
     * 
     * @abstract
     */
    abstract class AbstractTask implements TaskInterface
    {
        /**
         * Stores the state modified state of the data. I.e. if the job needs recreating.
         *
         * @var bool
         */
        protected $modified = true;
        
        /**
         * Stores the task string for this task instance.
         *
         * @var string
         */
        protected $task;
        
        /**
         * Stores the data used to construct the job for this task.
         *
         * @var array
         */
        protected $data = [];
        
        /**
         * {@inheritdoc}
         */
        public function getTask()
        {
            if ($this->modified) {
                try {
                    $this->buildTask();
                } catch (\Exception $ex) {
                    throw $ex;
                }
            }
            return $this->task;
        }
        
        /**
         * Builds the task and returns it.
         * 
         * @throws \Sycamore\Scheduler\Exception\MissingDataException
         * @throws \Sycamore\Scheduler\Exception\MissingExecuteTimeException
         * 
         * @abstract
         */
        abstract protected function buildTask();
        
        /**
         * {@inheritdoc}
         */
        public function setTask($task)
        {
            if (!is_string($task)) {
                throw new \InvalidArgumentException("The task is expected to be a string.");
            }
            $this->modified = false;
            $this->task = $task;
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function setJob($job)
        {
            if (!is_string($job)) {
                throw new \InvalidArgumentException("The job is expected to be a string.");
            }
            return $this->set("job", $job);
        }
        
        /**
         * {@inheritdoc}
         */
        public function getJob()
        {
            try {
                return $this->get("job");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasJob() {
            return $this->has("job");
        }
        
        /**
         * {@inheritdoc}
         */
        public function setExecutiveDate($date)
        {
            if (!is_array($date)) {
                throw new \InvalidArgumentException("The date should be in an array.");
            } else {
                foreach ($date as $key => $val) {
                    if (!is_numeric($val) || !in_array($key, ["day", "month", "year"])) {
                        throw new \InvalidArgumentException("The date array was invalid.");
                    }
                }
            }
            return $this->set("executiveDate", $date);
        }
        
        /**
         * {@inheritdoc}
         */
        public function getExecutiveDate()
        {
            try {
                return $this->get("executiveDate");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasExecutiveDate() {
            return $this->has("executiveDate");
        }
        
        /**
         * {@inheritdoc}
         */
        public function setExecutiveTime($time)
        {
            if (!is_array($time)) {
                throw new \InvalidArgumentException("The time should be in an array.");
            } else {
                foreach ($time as $key => $val) {
                    if (!is_numeric($val) || !in_array($key, ["hour", "minutes", "seconds"])) {
                        throw new \InvalidArgumentException("The time array was invalid.");
                    }
                }
            }
            return $this->set("executiveTime", $time);
        }
        
        /**
         * {@inheritdoc}
         */
        public function getExecutiveTime()
        {
            try {
                return $this->get("executiveTime");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasExecutiveTime() {
            return $this->has("executiveTime");
        }
        
        /**
         * {@inheritdoc}
         */
        public function setExecutiveMonths($months)
        {
            if (!is_string($months)) {
                throw new \InvalidArgumentException("Months were expected to be passed in as a string.");
            }
            return $this->set("executiveMonths", $months);
        }
        
        /**
         * {@inheritdoc}
         */
        public function getExecutiveMonths()
        {
            try {
                return $this->get("executiveMonths");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasExecutiveMonths()
        {
            return $this->has("executiveMonths");
        }
        
        /**
         * {@inheritdoc}
         */
        public function setExecutiveDays($days)
        {
            if (!is_string($days)) {
                throw new \InvalidArgumentException("Days were expected to be passed in as a string.");
            }
            return $this->set("executiveDays", $days);
        }
        
        /**
         * {@inheritdoc}
         */
        public function getExecutiveDays()
        {
            try {
                return $this->get("executiveDays");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasExecutiveDays()
        {
            return $this->has("executiveDays");
        }
        
        /**
         * {@inheritdoc}
         */
        public function setScheduleType($scheduleType)
        {
            switch($scheduleType) {
                case self::SCHEDULE_ONCE:
                case self::SCHEDULE_MINUTE:
                case self::SCHEDULE_HOURLY:
                case self::SCHEDULE_DAILY:
                case self::SCHEDULE_WEEKLY:
                case self::SCHEDULE_MONTHLY:
                    return $this->set("scheduleType", $scheduleType);
                default:
                    throw new \InvalidArgumentException("Provided schedule type was not a valid option.");
            }
            
            return $this;
        }
        
        /**
         * {@inheritdoc}
         */
        public function getScheduleType()
        {
            try {
                return $this->get("scheduleType");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasScheduleType()
        {
            return $this->has("scheduleType");
        }
        
        /**
         * {@inheritdoc}
         */
        public function getId()
        {
            try {
                return $this->get("id");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function hasId()
        {
            return $this->has("id");
        }
        
        /**
         * {@inheritdoc}
         */
        public function toArray()
        {
            return $this->data;
        }
        
        /**
         * Sets the given value to the given key in data.
         * 
         * @param string $key
         * @param mixed $value
         * 
         * @return \Sycamore\Scheduler\AbstractTask
         */
        protected function set($key, $value)
        {
            $this->data[$key] = $value;
            $this->modified = true;
            return $this;
        }
        
        /**
         * Gets the value at the given key if it exists.
         * 
         * @param string $key
         * 
         * @return mixed
         * 
         * @throws \Exception
         */
        protected function get($key)
        {
            if (!isset($this->data[$key])) {
                throw new \Exception("$key has not been set or is invalid.");
            }
            return $this->data[$key];
        }
        
        /**
         * Determines if the given key has been set in this task.
         * 
         * @param string $key
         * 
         * @return bool
         */
        protected function has($key)
        {
            return isset($this->data[$key]);
        }
    }