<?php
    namespace Sycamore\Scheduler\Task;
    
    use Sycamore\OS\FileSystem;
    use Sycamore\Scheduler\Exception\MissingExecuteTimeException;
    use Sycamore\Scheduler\Exception\MissingDataException;
    use Sycamore\Scheduler\Exception\UnusedTaskException;
    use Sycamore\Scheduler\Task\AbstractTask;
    use Sycamore\Stdlib\UniqueID;
    
    /**
     * The UNIX-specific task implementation.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class UnixTask extends AbstractTask
    {
        /**
         * {@inheritdoc}
         */
        public function getTaskRm()
        {
            if (!$this->hasId()) {
                throw new UnusedTaskException("Task has not been used yet, so no ID to construct a remove command with.");
            }
            if ($this->getScheduleType() != self::SCHEDULE_ONCE) {
                return false;
            }
            return "atrm {$this->getId()}";
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
            
            // Get the schedule type.
            $st = $this->getScheduleType();
            
            // Differentiate between execute once and execute multiple times (at vs crontab).
            if ($st == self::SCHEDULE_ONCE) {
                $task = $this->constructAtTaskString();
            } else {
                $task = $this->constructCronTaskString();
            }
            
            // Set the modified state to false.
            $this->modified = false;
            
            // Set the task string to the newly built task.
            $this->task = $task;
        }
        
        /**
         * Construct the at command for the task.
         * 
         * @return string The resulting at command.
         */
        protected function constructAtTaskString()
        {
            $task = "at ";

            // Add start time.
            $time = $this->getExecutiveTime();
            $task .= (isset($time["hour"]) ? $time["hour"] : "00") . ":" . (isset($time["minutes"]) ? $time["minutes"] : "00");

            // If a date for execution is specified, add it.
            if ($this->hasExecutiveDate()) {
                $date = $this->getExecutiveDate();
                $task .= " " . (isset($date["day"]) ? $date["day"] : date("d")) . "." . (isset($date["month"]) ? $date["month"] : date("m")) . "." . (isset($date["year"]) ? $date["year"] : date("Y"));
            }

            // Create a temporary shell script for the command (deletes itself on execution).
            $this->setShellFileHandle();
            $filehandle = $this->getShellFileHandle();
            //$filepath = TEMP_DIRECTORY . "/atcmd/" . $filehandle;
            $filepath = "/tmp/atcmd/" . $filehandle;
            FileSystem::filePutContents($filepath, $this->getJob() . "\nrm -f $filepath");

            // Add job to the command line call.
            $task .= " -f $filepath";
            
            return $task;
        }
        
        /**
         * Construct the crontab task string for the task.
         * 
         * @return string The resulting crontab task string.
         */
        protected function constructCronTaskString()
        {
            // Set the ID for this task.
            $this->set("id", UniqueID::generate());
            
            // Add time components to cron task.
            if ($this->hasExecutiveTime()) {
                $time = $this->getExecutiveTime();
                $task .= (isset($time["minutes"]) ? $time["minutes"] : "*") . " " . (isset($time["hour"]) ? $time["hour"] : "*") . " ";
            } else {
                $task .= "* * ";
            }

            // Add date components to cron task.
            $hasExecutiveMonths = $this->hasExecutiveMonths();
            $hasExecutiveDate = $this->hasExecutiveDate();
            /// Add day and month from date if no executive month specified, otherwise day from date and months from executive months.
            if (!$hasExecutiveMonths && $hasExecutiveDate) {
                $date = $this->getExecutiveDate();
                $task .= (isset($date["day"]) ? $date["day"] : "*") . " " . (isset($date["month"]) ? $date["month"] : "*") . " ";
            } else if ($hasExecutiveMonths) {
                if ($hasExecutiveDate) {
                    $date = $this->getExecutiveDate();
                    $task .= (isset($date["day"]) ? $date["day"] : "*") . " ";
                } else {
                    $task .= "* ";
                }
                $task .= $this->getExecutiveMonths() . " ";
            }
            /// Add executive days if provided.
            if ($this->hasExecutiveDays()) {
                $task .= $this->getExecutiveDays() . " ";
            } else {
                $task .= "* ";
            }

            // Add job.
            $task .= $this->getJob();
            
            return $task;
        }
        
        /**
         * Sets the file handle for this task.
         */
        public function setShellFileHandle()
        {
            $this->set("shellFileHandle", Rand::getString(16, Rand::ALPHANUMERIC) . ".sh");
        }
        
        /**
         * Gets the file handle for this task.
         */
        public function getShellFileHandle()
        {
            return $this->get("shellFileHandle");
        }
        
        /**
         * Determines if a file handle has already been set for this task.
         */
        public function hasShellFileHandle()
        {
            return $this->has("shellFileHandle");
        }
        
        /**
         * Sets the ID of the task based on result of creating the task in the OS.
         * Should ONLY be used for SCHEDULE_ONCE tasks.
         * 
         * @param string $creationResult The result of creating the task in the OS shell.
         * 
         * @throws \InvalidArgumentException If the creation result parameter is not a string.
         * @throws \BadMethodCallException If the call is made on a task already set as a SCHEDULE_ONCE task.
         */
        public function setId($creationResult)
        {
            if (!is_string($creationResult)) {
                throw new \InvalidArgumentException("Creation result was expected to be a string.");
            }
            if ($this->hasScheduleType() && $this->getScheduleType() != self::SCHEDULE_ONCE) {
                throw new \BadMethodCallException("Should only set ID for SCHEDULE_ONCE tasks.");
            }
            $this->set("id", explode(" ", $creationResult)[1]);
        }
    }
