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
    
    /**
     * Helper class for dealing with directories.
     */
    class FileSystem
    {
        /**
         * Delete a directory if empty or directory and contents if force flag is true.
         * If the path points directly to a file, that file will be deleted.
         * 
         * @param string $path
         * @param bool $force
         * 
         * @return boolean
         */
        public static function delete($path, $force = false)
        {
            if (is_dir($path)) {
                $files = array_diff(scandir($path), array('.', '..'));
                if (!empty($files) && !$force) {
                    return false;
                }
                foreach ($files as $file) {
                    self::delete(realpath($path) . '/' . $file);
                }
                return rmdir($path);
            } else if (is_file($path)) {
                return unlink($path);
            }
            return false;
        }
        
        /**
         * If related directories to filename do not exist they are created given force parameter is true.
         * If filename does not exist the file is created, otherwise the existing file is overwritten unless FILE_APPEND is set.
         * 
         * @param string $filename
         * @param mixed $data
         * @param bool $force
         * @param int $flags
         * @param resource $context
         * 
         * @return int
         */
        public static function filePutContents($filename, $data, $force = true, $flags = 0, $context = NULL)
        {
            if ($force) {
                $filepathParts = explode(DIRECTORY_SEPARATOR, $filename);
                $file = array_pop($filepathParts);
                if (empty($filepathParts[0])) {
                    array_shift($filepathParts);
                }
                $filepath = "";
                foreach ($filepathParts as $part) {
                    if (!is_dir($filepath .= DIRECTORY_SEPARATOR . $part)) {
                        mkdir($filepath);
                    }
                }
            }
            
            return file_put_contents($filename, $data, $flags, $context);
        }
    }