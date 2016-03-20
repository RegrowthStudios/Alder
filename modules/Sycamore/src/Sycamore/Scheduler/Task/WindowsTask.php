<?php
    namespace Sycamore\Scheduler\Task;
    
    use Sycamore\Scheduler\Exception\MissingDataException;
    use Sycamore\Scheduler\Exception\MissingExecuteTimeException;
    use Sycamore\Scheduler\Exception\UnusedTaskException;
    use Sycamore\Scheduler\Task\AbstractTask;
    use Sycamore\Stdlib\UniqueID;
    
    /**
     * The Windows-specific task implementation.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class WindowsTask extends AbstractTask
    {
        const MODIFIER_LASTDAY = "LASTDAY";
        const MODIFIER_FIRST = "FIRST";
        const MODIFIER_SECOND = "SECOND";
        const MODIFIER_THIRD = "THIRD";
        const MODIFIER_FOURTH = "FOURTH";
        const MODIFIER_LAST = "LAST";
        
        const MONTH_ALL = "*";
        
        /**
         * {@inheritdoc}
         */
        public function getTaskRm()
        {
            if (!$this->hasId()) {
                throw new UnusedTaskException("Task has not been used yet, so no ID to construct a remove command with.");
            }
            return "schtasks /Delete /Tn {$this->getId()} /F";
        }
        
        /**
         * {@inheritdoc}
         */
        protected function buildTask()
        {
            if (!$this->hasScheduleType()
                    || !$this->hasJob()) {
                throw new MissingDataException("This task does not have the necessary data to be constructed.");
            }
            if ($this->getScheduleType() == self::SCHEDULE_ONCE && !$this->hasExecutiveTime()) {
                throw new MissingExecuteTimeException("A start time must be specified for a once-executed task.");
            }
            
            // Set the ID for this task.
            $this->set("id", UniqueID::generate());
            
            // Get the schedule type.
            $st = $this->getScheduleType();
            
            // Prepare the initial details of the task.
            $task = "schtasks /create /tn " . $this->getId(NULL) . " /tr " . $this->getJob() . " /sc " . $st;
            
            // If a modifier is allowed and one exists for this task, add it.
            // CAUTION: If FIRST, SECOND, THIRD, FOURTH, or LAST for modifier of MONTHLY, set a day "/d" too!
            if ($st !== self::SCHEDULE_ONCE && $this->hasModifier()) {
                $task .= " /mo " . $this->getModifer();
            }
            
            // If weekly or monthly, and executive days have been specified, add them.
            if (($st == self::SCHEDULE_WEEKLY || $st == self::SCHEDULE_MONTHLY) && $this->hasExecutiveDays()) {
                $task .= " /d " . $this->getExecutiveDays();
            }
            
            // If monthly, and executive months have been specified, add them.
            if ($st == self::SCHEDULE_MONTHLY && $this->hasExecutiveMonths()) {
                $task .= " /m " . $this->getExecutiveMonths();
            }
            
            // If minutely or hourly, and a duration has been specified, add it.
            if (($st == self::SCHEDULE_MINUTE || $st == self::SCHEDULE_HOURLY) && $this->hasDuration()) {
                $task .= " /du " . $this->getDuration();
            }
            
            // If a start date is specified, add it.
            if ($this->hasExecutiveDate()) {
                $date = $this->getExecutiveDate();
                $task .= " /sd " . (isset($date["day"]) ? $date["day"] : date("d")) . "/" . (isset($date["month"]) ? $date["month"] : date("m")) . "/" . (isset($date["year"]) ? $date["year"] : date("Y"));
            }
            
            // If a start time is specified, add it.
            if ($this->hasExecutiveTime()) {
                $time = $this->getExecutiveTime();
                $task .= " /st " . (isset($time["hour"]) ? $time["hour"] : "00") . ":" . (isset($time["minutes"]) ? $time["minutes"] : "00");
            }
            
            // Set the modified state to false.
            $this->modified = false;
            
            // Set the task string to the newly built task.
            $this->task = $task;
        }
        
        /**
         * Set the modifier for the task given the schedule type.
         * 
         * @param string $modifier The modifier for this task.
         * 
         * @return \Sycamore\Scheduler\Task\WindowsTask The task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $modifier is not a string.
         */
        public function setModifier($modifier)
        {
            if (!is_string($modifier)) {
                throw new \InvalidArgumentException("The modifier was expected to be passed in as a string.");
            }
            return $this->set("modifier", $modifier);
        }
        
        /**
         * Gets the modifier for this task.
         *
         * @return string The modifier for this task.
         * 
         * @throws \Exception If the modifier has not yet been set for this task.
         */
        public function getModifier()
        {
            try {
                return $this->get("modifier");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * Determines if the modifier has been set for this task.
         * 
         * @return bool True if the modifier has been set, false otherwise.
         */
        public function hasModifier()
        {
            return $this->has("modifier");
        }
        
        /**
         * Set the duration for the task.
         * Duration should be in an [HHHH:MM] format.
         * 
         * @param string $duration The duration to execute this task over.
         * 
         * @return \Sycamore\Scheduler\Task\WindowsTask This task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $duration is not a string.
         */
        public function setDuration($duration)
        {
            if (!is_string($duration)) {
                throw new \InvalidArgumentException("The duration was expected to be passed in as a string.");
            }
            return $this->set("duration", $duration);
        }
        
        /**
         * Gets the duration for this task.
         *
         * @return string The duration of this task.
         * 
         * @throws \Exception If the duration for this task is not yet set.
         */
        public function getDuration()
        {
            try {
                return $this->get("duration");
            } catch (\Exception $ex) {
                throw $ex;
            }
        }
        
        /**
         * Determines if the duration has been set for this task.
         * 
         * @return bool True if the duration is set for this task, false otherwise.
         */
        public function hasDuration()
        {
            return $this->has("duration");
        }
    }
