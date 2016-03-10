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

    namespace Sycamore\Scheduler\Adapter;
    
    use Sycamore\OS\Shell;
    use Sycamore\Scheduler\Adapter\AdapterInterface;
    
    class WindowsAdapter implements AdapterInterface
    {
        /**
         * {@inheritdoc}
         */
        public function addTask(\Sycamore\Scheduler\Task\TaskInterface $task)
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
        public function removeTask(\Sycamore\Scheduler\Task\TaskInterface $task)
        {
            // Remove task via shell.
            $result = Shell::execute($task->getTaskRm());
            
            // Return result of execution of task removal.
            return $result;
        }
    }