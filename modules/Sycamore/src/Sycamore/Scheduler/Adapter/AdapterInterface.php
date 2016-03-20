<?php
    namespace Sycamore\Scheduler\Adapter;
    
    use Sycamore\Scheduler\Task\TaskInterface;
    
    /**
     * Interface for scheduling adapters.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    interface AdapterInterface
    {
        /**
         * Adds the provided task to the OS schedule.
         * 
         * @param \Sycamore\Scheduler\Task\TaskInterface $task The task to be added.
         * 
         * @return bool True on success, false otherwise.
         */
        public function addTask(TaskInterface& $task);
        
        /**
         * Removes the provided task from the OS schedule.
         * 
         * @param \Sycamore\Scheduler\Task\TaskInterface $task The task to be removed.
         * 
         * @return bool True on success, false otherwise.
         */
        public function removeTask(TaskInterface& $task);
    }
