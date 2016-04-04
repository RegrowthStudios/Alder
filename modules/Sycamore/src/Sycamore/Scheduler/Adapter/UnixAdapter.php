<?php
    namespace Sycamore\Scheduler\Adapter;
    
    use Sycamore\OS\FileSystem;
    use Sycamore\OS\Shell;
    use Sycamore\Scheduler\Adapter\AdapterInterface;
    use Sycamore\Scheduler\Task\TaskInterface;
    
    /**
     * Scheduling adapter for UNIX systems.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class UnixAdapter implements AdapterInterface
    {
        const CRONTAB_FILEPATH = TEMP_DIRECTORY . "/crontab/crontab.txt";
        
        /**
         * {@inheritdoc}
         */
        public function addTask(TaskInterface& $task)
        {
            if ($task->getScheduleType() == TaskInterface::SCHEDULE_ONCE) {
                // Ensure GB-styled dates and times work as expected.
                setlocale(LC_TIME, "en_GB");

                // Add task via shell.
                $result = Shell::execute($task->getTask());

                // Reset to default locale afterwards.
                setlocale(LC_TIME, "");
            } else {
                // Cron task.
                $result = $this->addCronTask($task);
            }
            
            // Return result of execution of task.
            return $result;
        }
        
        /**
         * Adds the given task to the crontab.
         * 
         * @param \Sycamore\Scheduler\Task\TaskInterface $task The task to add a cron job for.
         * 
         * @return boolean True if successful (for now only result).
         */
        protected function addCronTask(TaskInterface& $task)
        {
            // Prepare temporary crontab file.
            $this->constructCrontabFile();
            
            // Put task into file.
            FileSystem::filePutContents(self::CRONTAB_FILEPATH, $task->getTask(), FILE_APPEND);
            
            // Apply file as crontab.
            Shell::execute("crontab " . self::CRONTAB_FILEPATH);
            
            // Delete file.
            unlink(self::CRONTAB_FILEPATH);
            
            // Return true on success.
            return true;
        }
        
        /**
         * {@inheritdoc}
         */
        public function removeTask(TaskInterface& $task)
        {
            if ($task->getScheduleType() == TaskInterface::SCHEDULE_ONCE) {
                // Remove task via shell.
                $result = Shell::execute($task->getRemoveTaskCommand());
            } else {
                // Remove cron task.
                $result = $this->removeCronTask($task);
            }
            
            // Return result of execution of task removal.
            return $result;
        }
        
        /**
         * Removes the given task from the crontab if it exists.
         * 
         * @param TaskInterface $task The task to be removed.
         * 
         * @return boolean True on successful remove, false if no task matched the given task in the existing crontab.
         */
        protected function removeCronTask(TaskInterface& $task)
        {
            // Prepare temporary crontab file.
            $this->constructCrontabFile();
            
            // Read existing tasks into an array.
            $existingCronTasks = file(self::CRONTAB_FILEPATH, FILE_IGNORE_NEW_LINES);
            
            // If no cron tasks exist, fail.
            if (empty($existingCronTasks)) {
                $this->removeFile(self::CRONTAB_FILEPATH);
                return false;
            }
            
            // Count current cron tasks.
            $currentTaskCount = count($existingCronTasks);
            
            // Determine which tasks we want to keep.
            $keptCronTasks = preg_grep("/" . $task->getTask() . "/", $existingCronTasks, PREG_GREP_INVERT);
            
            // Remove old temporary file.
            unlink(self::CRONTAB_FILEPATH);
            
            // If no change in task count, then no tasks are to be removed.
            // If there is a change, delete the current crontab and reconstruct it
            // without the to-be-removed task(s).
            if ($currentTaskCount === count($keptCronTasks)) {
                return false;
            } else {
                // Remove current crontab.
                Shell::execute("crontab -r");
                
                // Create new file.
                foreach ($keptCronTasks as $taskStr) {
                    // Put task into file.
                    FileSystem::filePutContents(self::CRONTAB_FILEPATH, $taskStr, FILE_APPEND);
                }
                
                // Apply file as crontab.
                Shell::execute("crontab " . self::CRONTAB_FILEPATH);

                // Delete file.
                unlink(self::CRONTAB_FILEPATH);
            }

            // Return true on success.
            return true;
        }
        
        /**
         * Constructs the temporary crontab file for the system.
         */
        protected function constructCrontabFile()
        {
            // If not already constructed, construct it.
            if (!file_exists(self::CRONTAB_FILEPATH)) {
                Shell::execute("crontab -l > " . self::CRONTAB_FILEPATH
                        . " && [ -f " . self::CRONTAB_FILEPATH . " ] || > "
                        . self::CRONTAB_FILEPATH);
            }
        }
    }
