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
    
    class Attachment
    {
        protected $fileHandle;

        protected $fileType;

        protected $uniqueId;
        
        public function __construct()
        {
        }
        
        public function setFileHandle($handle)
        {
            if (!is_string($handle)) {
                throw new \InvalidArgumentException("Attachment file handle must be a string.");
            }
            $this->fileHandle = $handle;
            
            return $this;
        }
        
        public function setFileType($type)
        {
            if (!is_string($type)) {
                throw new \InvalidArgumentException("File type expected to be a string.");
            }
            $this->fileType = $type;
            
            return $this;
        }
    }
    