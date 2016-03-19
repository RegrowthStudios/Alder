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
    
    use Sycamore\Scheduler\Adapter\AdapterInterface;
    use Sycamore\Scheduler\Adapter\UnixAdapter;
    use Sycamore\Scheduler\Adapter\WindowsAdapter;
    use Sycamore\Scheduler\Task\TaskInterface;
    use Sycamore\Stdlib\ArrayUtils;
    
    class Scheduler
    {
        /**
         * Stores the adapter appropriate for the given OS.
         *
         * @var \Sycamore\Scheduler\AdapterInterface
         */
        protected $adapter;
        
        public function __construct($adapter = NULL)
        {
            if ($adapter instanceof AdapterInterface) {
                $taskAdapter = $adapter;
            } else if (is_string($adapter)) {
                if (class_exists($adapter)) {
                    $taskAdapter = new $adapter();
                } else {
                    throw new \InvalidArgumentException("The scheduler adapter class name provided does not map to an existing adapter.");
                }
            } else {
                if (OS == WINDOWS) {
                    $taskAdapter = new WindowsAdapter();
                } else {
                    // Not definitely UNIX or UNIX-like, but probably.
                    // TODO(Matthew): Consider safer solution than assuming non Windows is always UNIX or UNIX-like.
                    $taskAdapter = new UnixAdapter();
                }
            }
            $this->adapter = $taskAdapter;
        }
        
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
        public function addTasks(& $tasks)
        {
            // Ensure tasks are in array-like form.
            try {
                $validTasks = ArrayUtils::validateArrayLike($tasks, get_class($this), true);
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
        public function addTask(TaskInterface& $task)
        {
            $this->adapter->addTask($task);
            
            // Return true on success.
            return true;
        }
        
        public function removeTasks(& $tasks)
        {
            // Ensure tasks are in array-like form.
            try {
                $validTasks = ArrayUtils::validateArrayLike($tasks, get_class($this), true);
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
        
        /**
         * Removes the provied task from the OS.
         * 
         * @param \Sycamore\Scheduler\Task\TaskInterface $task The task to be removed.
         * 
         * @return boolean True if successful (only result for now).
         * 
         * @throws \InvalidTaskException if the task provided has no ID (i.e. has not been added to the OS).
         */
        public function removeTask(TaskInterface&  $task)
        {
            // Ensure task has valid task ID.
            if (!$task->hasId()) {
                throw new \InvalidTaskException("The task provided has no ID, therefore cannot be removed.");
            }
            
            $this->adapter->removeTask($task);
            
            return true;
        }
    }
