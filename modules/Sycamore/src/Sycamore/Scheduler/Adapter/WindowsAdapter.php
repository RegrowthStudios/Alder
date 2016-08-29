<?php
    namespace Sycamore\Scheduler\Adapter;
    
    use Sycamore\OS\Shell;
    use Sycamore\Scheduler\Adapter\AdapterInterface;
    use Sycamore\Scheduler\Task\TaskInterface;
    
    /**
     * Scheduling adapter for Windows systems.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class WindowsAdapter implements AdapterInterface
    {
        /**
         * {@inheritdoc}
         */
        public function addTask(TaskInterface& $task)
        {
            // Ensure GB-styled dates and times work as expected.
            setlocale(LC_TIME, "en_GB");
            
            // Add task via shell.
            $result = Shell::execute($task->getTask());
            
            // Reset to default locale afterwards.
            setlocale(LC_TIME, "");
            
            // Return result of execution of task.
            return $result;
        }
        
        /**
         * {@inheritdoc}
         */
        public function removeTask(TaskInterface& $task)
        {
            // Remove task via shell.
            $result = Shell::execute($task->getRemoveTaskCommand());
            
            // Return result of execution of task removal.
            return $result;
        }
    }
