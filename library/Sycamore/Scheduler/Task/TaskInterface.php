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
         * @return string
         * 
         * @throws \Exception
         */
        public function getTask();
        
        /**
         * Returns the command to remove this task, or false if
         * the task removal reqires more work than just the execution
         * of a command (e.g. crontab).
         * 
         * @return string
         * 
         * @throws \Exception
         */
        public function getTaskRm();
        
        /**
         * Sets the task string to be exactly the given string.
         * 
         * @param string $task
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setTask($task);
        
        /**
         * Sets the program or command to be ran by the task.
         * 
         * @param string $job
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setJob($job);
        
        /**
         * Gets the program or command to be ran by the task.
         * 
         * @return string
         * 
         * @throws \Exception
         */
        public function getJob();
        
        /**
         * Determines if a job has been set for this task.
         * 
         * @return bool
         */
        public function hasJob();
        
        /**
         * Sets the date for this task to be executed.
         * Only the needed data points need be passed in. I.e. a 
         * time need not be specified if it is a time-insensitive 
         * clean up task.
         * 
         * @param array $date
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setExecutiveDate($date);
        
        /**
         * Gets the date the task is scheduled for occurring on.
         * 
         * @return int
         * 
         * @throws \Exception
         */
        public function getExecutiveDate();
        
        /**
         * Determines if a date has been set for this task.
         * 
         * @return bool
         */
        public function hasExecutiveDate();
        
        /**
         * Sets the time for this task to be executed.
         * Only the needed data points need be passed in. I.e. a 
         * time need not be specified if it is a time-insensitive 
         * clean up task.
         * 
         * @param array $time
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setExecutiveTime($time);
        
        /**
         * Gets the time the task is scheduled for occurring on.
         * 
         * @return int
         * 
         * @throws \Exception
         */
        public function getExecutiveTime();
        
        /**
         * Determines if a time has been set for this task.
         * 
         * @return bool
         */
        public function hasExecutiveTime();
        
        /**
         * Set the months in which to execute this task.
         * 
         * @param string $months
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setExecutiveMonths($months);
        
        /**
         * Gets the executive months for this task.
         *
         * @return string
         * 
         * @throws \Exception
         */
        public function getExecutiveMonths();
        
        /**
         * Determines if the executive months have been set for this task.
         * 
         * @return bool
         */
        public function hasExecutiveMonths();
        
        /**
         * Set the days on which to execute this task.
         * 
         * @param string $days
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setExecutiveDays($days);
        
        /**
         * Determines if the executive days have been set for this task.
         * 
         * @return bool
         */
        public function hasExecutiveDays();
        
        /**
         * Gets the executive days for this task.
         *
         * @return string
         * 
         * @throws \Exception
         */
        public function getExecutiveDays();
        
        /**
         * Sets the schedule type for this task.
         * 
         * @param string $scheduleType
         * 
         * @return self
         * 
         * @throws \InvalidArgumentException
         */
        public function setScheduleType($scheduleType);
        
        /**
         * Gets the schedule type of the task.
         * 
         * @return string
         * 
         * @throws \Exception
         */
        public function getScheduleType();
        
        /**
         * Determines if a schedule type has been set for this task.
         * 
         * @return bool
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
         * @returns bool
         */
        public function hasId();
        
        /**
         * Converts the task to an array and returns it. This array is 
         * compatible with the task factory.
         * 
         * @return array
         */
        public function toArray();
    }