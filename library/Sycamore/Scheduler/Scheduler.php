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

    namespace Sycamore\Scheduler;
    
    use Sycamore\Scheduler\Task\TaskInterface;
    use Sycamore\Stdlib\ArrayUtils\ArrayLikeValidation;
    use Sycamore\OS\FileSystem;
    use Sycamore\OS\Shell;
    
    class Scheduler
    {
        /**
         * Adds a set of tasks to the OS schedule.
         * 
         * @param array|\Traversable $tasks
         * 
         * @return bool
         * 
         * @throws \InvalidArgumentException if tasks are not in array-like form or if any 
         * individual task is not an instance of the task interface.
         */
        public static function addTasks(& $tasks)
        {
            // Ensure tasks are in array-like form.
            try {
                $validTasks = ArrayLikeValidation::validateData($tasks, get_class($this), true);
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            
            // Ensure individual tasks are of valid type.
            foreach ($validTasks as $validTask) {
                if (!$validTask instanceof TaskInterface) {
                    throw new \InvalidArgumentException("A task was not an instance of a TaskInterface");
                }
            }
            
            // Add tasks given all are valid.
            foreach ($validTasks as $validTask) {
                static::addTask($validTask);
            }
            
            // Return true on success.
            return true;
        }
        
        /**
         * Adds the provided task to the OS schedule.
         * 
         * @param \Sycamore\Scheduler\Task\TaskInterface $task
         * 
         * @return bool
         */
        public static function addTask(TaskInterface& $task)
        {
            // Ensure GB-styled dates and times work as expected.
            setlocale(LC_TIME, "en_GB");
            // Add task as per the type of task.
            if (OS == WINDOWS || $task->getScheduleType() == TaskInterface::SCHEDULE_ONCE) {
                $result = Shell::execute($task->getTask());
            } else {
                static::addCronTask($task);
            }
            
            // Return true on success.
            return true;
        }
        
        /**
         * Adds the provided cron task to the crontab.
         * 
         * @param TaskInterface $task
         * 
         * @return bool
         */
        protected static function addCronTask(TaskInterface& $task)
        {
            // Get task string and ID.
            $taskStr = $task->getTask();
            $id = $task->getId();
            
            // Construct filepath for crontab.
            $filepath = static::getCrontabFilepath($id);
            
            // Put task into file.
            FileSystem::filePutContents($filepath, $taskStr);
            
            // Apply file as crontab.
            $applyCronTaskCmd = "crontab $filepath";
            Shell::execute($applyCronTaskCmd);
            
            // Delete file.
            unlink($filepath);
            
            // Return true on success.
            return true;
        }
        
        public function removeTasks(& $tasks)
        {
            // Ensure tasks are in array-like form.
            try {
                $validTasks = ArrayLikeValidation::validateData($tasks, get_class($this), true);
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }
            
            // Ensure individual tasks are of valid type.
            foreach ($validTasks as $validTask) {
                if (!$validTask instanceof TaskInterface) {
                    throw new \InvalidArgumentException("A task was not an instance of a TaskInterface");
                } else if (!$validTask->hasId()) {
                    throw new \InvalidTaskException("The task provided has no ID, therefore cannot be removed.");
                }
            }
            
            // Add tasks given all are valid.
            foreach ($validTasks as $validTask) {
                try {
                    static::removeTask($validTask);
                } catch (\InvalidTaskException $ex) {
                    throw $ex; // Should NOT get here.
                }
            }
            
            // Return true on success.
            return true;
        }
        
        public function removeTask(TaskInterface&  $task)
        {
            // Attempt to grab task ID.
            try {
                $id = $task->getId();
            } catch (\Exception $ex) {
                throw new \InvalidTaskException("The task provided has no ID, therefore cannot be removed.");
            }
            
            
        }
        
        public function removeCronTask(TaskInterface& $task)
        {
        }
        
        /**
         * Constructs and returns the crontab filepath for the given crontab ID.
         * 
         * @param string $id
         * 
         * @return string
         */
        protected static function getCrontabFilepath($id)
        {
            return TEMP_DIRECTORY . "/crontab/" . $id . ".txt";
        }
    }
