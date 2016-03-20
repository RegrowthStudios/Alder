<?php
    namespace Sycamore\Scheduler;
    
    use Sycamore\Scheduler\Adapter\AdapterInterface;
    use Sycamore\Scheduler\Adapter\UnixAdapter;
    use Sycamore\Scheduler\Adapter\WindowsAdapter;
    use Sycamore\Scheduler\Task\TaskInterface;
    use Sycamore\Stdlib\ArrayUtils;
    
    /**
     * Scheduler class for scheduling and removing tasks in the host OS.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Scheduler
    {
        /**
         * Stores the adapter appropriate for the given OS.
         *
         * @var \Sycamore\Scheduler\AdapterInterface
         */
        protected $adapter;
        
        /**
         * Prepares this scheduler instance with an adapter.
         * 
         * @param \Sycamore\Scheduler\Adapter\AdapterInterface|string $adapter The name of the adapter class or an instance of a valid adapter to overide default adapter.
         * 
         * @throws \InvalidArgumentException If $adapter is a string pointing to an invalid or non-existent class.
         */
        public function __construct($adapter = NULL)
        {
            if ($adapter instanceof AdapterInterface) {
                $taskAdapter = $adapter;
            } else if (is_string($adapter)) {
                if (class_exists($adapter)) {
                    $taskAdapterTemp = new $adapter();
                    if (!($taskAdapterTemp instanceof AdapterInterface)) {
                        throw new \InvalidArgumentException("The class specified is not an instance of Sycamore\Scheduler\AdapterInterface.");
                    }
                    $taskAdapter = $taskAdapterTemp;
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
         * @param array|\Traversable $tasks The tasks to be added.
         * 
         * @return bool True on successful adding of tasks, false otherwise.
         * 
         * @throws \InvalidArgumentException If tasks are not in array-like form or if any 
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
         * @param \Sycamore\Scheduler\Task\TaskInterface $task The task to be added.
         * 
         * @return bool True on successful adding of task.
         */
        public function addTask(TaskInterface& $task)
        {
            $this->adapter->addTask($task);
            
            // Return true on success.
            return true;
        }
        
        /**
         * Removes the provided tasks from the OS schedule.
         * 
         * @param array|\Traversable $tasks The tasks to be removed.
         * 
         * @return bool True on successful removal.
         * 
         * @throws \InvalidArgumentException If $tasks or any of its contents are of invalid type.
         * @throws \InvalidTaskException If any task provided has not yet been added to be removed.
         */
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
         * @return bool True if successful.
         * 
         * @throws \InvalidTaskException If the task provided has no ID (i.e. has not been added to the OS).
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
