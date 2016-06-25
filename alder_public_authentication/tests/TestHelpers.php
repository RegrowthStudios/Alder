<?php

    namespace AlderTest;
    
    /**
     * Helper functions for tests.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class TestHelpers
    {
        /**
         * Destroys all contents of a directory. Intended for use with temporary directories in tests.
         * 
         * @param string $directory The directory to destroy the contents of.
         */
        public static function nukeDirectory($directory)
        {
            $pathIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
            foreach($pathIterator as $path) {
                if ($path->isDir() && !$path->isLink()) {
                    rmdir($path->getPathname());
                } else {
                    unlink($path->getPathname());
                }
            }
        }
    }
