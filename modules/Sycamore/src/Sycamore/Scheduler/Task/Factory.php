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

    use Sycamore\Stdlib\ArrayUtils;

    class Factory
    {
        /**
         * Creates a task object from the given data based on the OS the server is running in.
         *
         * @param array|\Traversable $data
         *
         * @return \Sycamore\Scheduler\Task\TaskInterface
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
