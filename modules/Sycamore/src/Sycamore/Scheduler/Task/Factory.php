<?php
    namespace Sycamore\Scheduler\Task;

    use Sycamore\Stdlib\ArrayUtils;

    /**
     * Factory for generating tasks from specification arrays.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Factory
    {
        /**
         * Creates a task object from the given data based on the OS the server is running in.
         *
         * @param array|\Traversable $data The data to create the task from.
         *
         * @return \Sycamore\Scheduler\Task\TaskInterface The resulting task.
         */
        public static function create($data)
        {
            try {
                $validatedData = ArrayUtils::validateArrayLike($data, get_class($this), true);
            } catch (\InvalidArgumentException $ex) {
                throw $ex;
            }

            $taskClassName = OS . "Task";
            $task = new $taskClassName();

            foreach ($validatedData as $key => $value) {
                $func = "set" . ucfirst($key);
                if (method_exists($task, $func)) {
                    $task->{$func}($value);
                } else {
                    throw new \InvalidArgumentException("No property, $key, exists in $taskClassName.");
                }
            }

            return $task;
        }
    }
