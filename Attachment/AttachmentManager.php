<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
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

    namespace Sycamore\Attachment;
    
    use Sycamore\Application;
    
    class AttachmentManager
    {
        /**
         * Creates a permanent instance of the uploaded attachment if possible.
         * 
         * @param \Sycamore\Row\Attachment $attachment
         * @param string $currentLocation
         * 
         * @return boolean
         */
        public static function createAttachment(\Sycamore\Row\Attachment $attachment, $currentLocation)
        {
            // Construct final file path.
            $filepath = self::constructFilepath($attachment->id, $attachment->fileHandle);
            
            // Attempt to move uploaded file to the permanent destination.
            if (move_uploaded_file($currentLocation, $filepath)) {
                return true;
            }
            return false;
        }
        
        /**
         * Removes the given attachment if it exists.
         * 
         * @param \Sycamore\Row\Attachment $attachment
         * 
         * @return boolean
         */
        public static function removeAttachment(\Sycamore\Row\Attachment $attachment)
        {
            // Construct final file path.
            $filepath = self::constructFilepath($attachment->id, $attachment->fileHandle);
            
            // Unlink file if it exists.
            if (file_exists($filepath) && unlink($filepath)) {
                return true;
            }
            return false;
        }
        
        /**
         * Provides a file pointer to the given attachment, or false on failure.
         * 
         * @param \Sycamore\Row\Attachment $attachment
         * 
         * @return resource|boolean
         */
        public static function getAttachment(\Sycamore\Row\Attachment $attachment)
        {
            // Construct final file path.
            $filepath = self::constructFilepath($attachment->id, $attachment->fileHandle);
            
            // Return result of calling fopen on filepath.
            return fopen($filepath, "r");
        }
        
        /**
         * Empty constructor.
         */
        protected function __construct()
        {
        }
        
        protected static function constructFilepath($id, $fileHandle)
        {
            return Application::getConfig()->attachment->directory . $id . pathinfo($attachment->fileHandle, PATHINFO_EXTENSION);
        }
    }
