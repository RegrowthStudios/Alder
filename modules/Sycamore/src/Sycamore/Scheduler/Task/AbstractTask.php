<?php
    namespace Sycamore\Scheduler\Task;
    
    use Sycamore\Scheduler\Exception\MissingDataException;
    use Sycamore\Scheduler\Exception\MissingExecuteTimeException;
    use Sycamore\Scheduler\Task\TaskInterface;
    
    /**
     * Abstract task class used as a base for all task objects.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
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
                } catch (MissingDataException $ex) {
                    throw $ex;
                } catch (MissingExecuteTimeException $ex) {
                    throw $ex;
                }
            }
            return $this->task;
        }
        
        /**
         * Builds the task and returns it.
         * 
         * @throws \Sycamore\Scheduler\Exception\MissingDataException If data is missing needed to build the task.
         * @throws \Sycamore\Scheduler\Exception\MissingExecuteTimeException If the executive time is missing.
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
         * @param string $key The key of the value to set.
         * @param mixed $value The value to set.
         * 
         * @return \Sycamore\Scheduler\AbstractTask This instance of task for chaining sets.
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
         * @param string $key The key of the value to get.
         * 
         * @return mixed The value fetched.
         * 
         * @throws \InvalidArgumentException If the key given is invalid or not yet set for this task.
         */
        protected function get($key)
        {
            if (!isset($this->data[$key])) {
                throw new \InvalidArgumentExceptionException("$key has not been set or is invalid.");
            }
            return $this->data[$key];
        }
        
        /**
         * Determines if the given key has been set in this task.
         * 
         * @param string $key The key to ascertain if a value exists for it.
         * 
         * @return bool True if a value exists at the given key, false otherwise.
         */
        protected function has($key)
        {
            return isset($this->data[$key]);
        }
    }