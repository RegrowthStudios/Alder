<?php

/* 
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
 */

    namespace Sycamore\OS;
    
    class Shell
    {
        /**
         * Executes a supplied shell command at the supplied directory or app directory if none specified.
         * 
         * @param string $command
         * @param string $dir
         * 
         * @return mixed Returns NULL if no output or an error occurred, otherwise the output of the executed command.
         * 
         * @throws \InvalidArgumentException
         * @throws Exception
         */
        public static function execute($command, $dir = APP_DIRECTORY)
        {
            if (!is_string($command)) {
                throw new \InvalidArgumentException("Command supplied was not a string.");
            }
            
            if (is_string($dir)) {
                chdir($dir);
            }
            return shell_exec($command);
        }
    }