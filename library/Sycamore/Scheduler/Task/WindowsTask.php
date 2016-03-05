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
    
    use Sycamore\Scheduler\Exception\MissingDataException;
    use Sycamore\Scheduler\Exception\MissingExecuteTimeException;
    use Sycamore\Scheduler\Exception\UnusedTaskException;
    use Sycamore\Scheduler\Task\AbstractTask;
    use Sycamore\Stdlib\Rand;
    
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
            $this->set("id", uniqid(Rand::getString(5, Rand::ALPHANUMERIC)));
            
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
         * @param string $modifier
         * 
         * @return \Sycamore\Scheduler\Task\WindowsTask
         * 
         * @throws \InvalidArgumentException
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
         * @return string
         * 
         * @throws \Exception
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
         * @return bool
         */
        public function hasModifier()
        {
            return $this->has("modifier");
        }
        
        /**
         * Set the duration for the task.
         * Duration should be in an [HHHH:MM] format.
         * 
         * @param string $duration
         * 
         * @return \Sycamore\Scheduler\Task\WindowsTask
         * 
         * @throws \InvalidArgumentException
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
         * @return string
         * 
         * @throws \Exception
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
         * @return bool
         */
        public function hasDuration()
        {
            return $this->has("duration");
        }
    }
