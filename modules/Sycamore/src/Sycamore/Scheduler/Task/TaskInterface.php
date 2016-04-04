<?php
    namespace Sycamore\Scheduler\Task;
    
    /**
     * Interface for all tasks.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    interface TaskInterface
    {
        const SCHEDULE_ONCE = "ONCE";
        const SCHEDULE_MINUTE = "MINUTE";
        const SCHEDULE_HOURLY = "HOURLY";
        const SCHEDULE_DAILY = "DAILY";
        const SCHEDULE_WEEKLY = "WEEKLY";
        const SCHEDULE_MONTHLY = "MONTHLY";
        
        /**
         * Returns the task resulting from this task instance.
         * Throws an exception if not enough data has been specified to construct the task string.
         * 
         * @return string The string form of the task.
         * 
         * @throws \Sycamore\Scheduler\Exception\MissingDataException If data is missing needed to create the task string.
         * @throws \Sycamore\Scheduler\Exception\MissingExecuteTimeException If the executive time is missing.
         */
        public function getTask();
        
        /**
         * Returns the command to remove this task, or false if
         * the task removal reqires more work than just the execution
         * of a command (e.g. crontab).
         * 
         * @return string The resulting task removal string.
         * 
         * @throws \Sycamore\Scheduler\Exception\UnusedTaskException If the task is yet to be used.
         */
        public function getRemoveTaskCommand();
        
        /**
         * Sets the task string to be exactly the given string.
         * 
         * @param string $task The string form of the task.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $task is not a string.
         */
        public function setTask($task);
        
        /**
         * Sets the program or command to be ran by the task.
         * 
         * @param string $job The job for this task.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $job is not a string.
         */
        public function setJob($job);
        
        /**
         * Gets the program or command to be ran by the task.
         * 
         * @return string The job for this task.
         * 
         * @throws \Exception If the job has not yet been set.
         */
        public function getJob();
        
        /**
         * Determines if a job has been set for this task.
         * 
         * @return bool True if this task has a job, false otherwise.
         */
        public function hasJob();
        
        /**
         * Sets the date for this task to be executed.
         * Only the needed data points need be passed in. I.e. a 
         * time need not be specified if it is a time-insensitive 
         * clean up task.
         * 
         * @param array $date The date to execute the task at.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $data is not an array.
         */
        public function setExecutiveDate($date);
        
        /**
         * Gets the date the task is scheduled for occurring on.
         * 
         * @return array The executive date for this task.
         * 
         * @throws \Exception If the executive date is not yet set for this task.
         */
        public function getExecutiveDate();
        
        /**
         * Determines if a date has been set for this task.
         * 
         * @return bool True if the executive date is set, false otherwise.
         */
        public function hasExecutiveDate();
        
        /**
         * Sets the time for this task to be executed.
         * Only the needed data points need be passed in. I.e. a 
         * time need not be specified if it is a time-insensitive 
         * clean up task.
         * 
         * @param array $time The time to execute this task at.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $time is not an array.
         */
        public function setExecutiveTime($time);
        
        /**
         * Gets the time the task is scheduled for occurring on.
         * 
         * @return array The time to execute this task at.
         * 
         * @throws \Exception If the executive time has not yet been set for this task.
         */
        public function getExecutiveTime();
        
        /**
         * Determines if a time has been set for this task.
         * 
         * @return bool True if the executive time is set, false otherwise.
         */
        public function hasExecutiveTime();
        
        /**
         * Set the months in which to execute this task.
         * 
         * @param string $months The months to execute this task on, comma-separated.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $months is not a string.
         */
        public function setExecutiveMonths($months);
        
        /**
         * Gets the executive months for this task.
         *
         * @return string The executive months for this task.
         * 
         * @throws \Exception If the executive months have not yet been set for this task.
         */
        public function getExecutiveMonths();
        
        /**
         * Determines if the executive months have been set for this task.
         * 
         * @return bool True if the executive months are set, false otherwise.
         */
        public function hasExecutiveMonths();
        
        /**
         * Set the days on which to execute this task.
         * 
         * @param string $days The days to execute this task on, comma-separated.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $days is not a string.
         */
        public function setExecutiveDays($days);
        
        /**
         * Determines if the executive days have been set for this task.
         * 
         * @return bool True if the executive days are set, false otherwise.
         */
        public function hasExecutiveDays();
        
        /**
         * Gets the executive days for this task.
         *
         * @return string The executive days for this task.
         * 
         * @throws \Exception If the executive days have not yet been set for this task.
         */
        public function getExecutiveDays();
        
        /**
         * Sets the schedule type for this task.
         * 
         * @param string $scheduleType The schedule type to set this task to.
         * 
         * @return \Sycamore\Scheduler\TaskInterface Own task instance for chaining sets.
         * 
         * @throws \InvalidArgumentException If $scheduleType is not one of the SCHEDULE_* consts defined in this interace.
         */
        public function setScheduleType($scheduleType);
        
        /**
         * Gets the schedule type of the task.
         * 
         * @return string The schedule type of this task.
         * 
         * @throws \Exception If the schedule type is not yet set for this task.
         */
        public function getScheduleType();
        
        /**
         * Determines if a schedule type has been set for this task.
         * 
         * @return bool True if the schedule type is set, false otherwise.
         */
        public function hasScheduleType();
        
        /**
         * Gets the identifier for this task after creation in OS.
         * 
         * @todo [Matthew] Consider better way to get ID given different method for cron/at/schtask.
         * 
         * @return string
         * 
         * @throws \Exception
         */
        public function getId();
        
        /**
         * Determines if the ID has been set.
         * 
         * @returns bool True if the ID is set, false otherwise.
         */
        public function hasId();
        
        /**
         * Converts the task to an array and returns it. This array is 
         * compatible with the task factory.
         * 
         * @return array The array form of this task.
         */
        public function toArray();
    }