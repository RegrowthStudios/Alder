<?php
    namespace Sycamore\OS;
    
    /**
     * Provides functionality to execute shell commands.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Shell
    {
        /**
         * Executes a supplied shell command at the supplied directory or app directory if none specified.
         * 
         * @param string $command The command to be executed.
         * @param array $ouput The output from the executed command is dumped in this array.
         * @param int $returnVar The status code returned from the command execution is dumped here.
         * @param string $dir The directory in which to execute the command.
         * 
         * @return mixed Returns NULL if no output or an error occurred, otherwise the output of the executed command.
         * 
         * @throws \InvalidArgumentException If command is not a string.
         */
        public static function execute($command, array& $output = NULL, & $returnVar = NULL, $dir = APP_DIRECTORY, $captureStdErr = true)
        {
            if (!is_string($command)) {
                throw new \InvalidArgumentException("Command supplied was not a string.");
            }
            
            if (is_string($dir)) {
                chdir($dir);
            }
            return exec($command . ($captureStdErr ? " 2>&1" : ""), $output, $returnVar);
        }
    }