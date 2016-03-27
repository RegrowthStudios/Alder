<?php
    namespace SycamoreTest;
    
    /**
     * Helper functions for tests.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class TestHelpers
    {
        public static function nukeDirectory($directory)
        {
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }
        }
    }
