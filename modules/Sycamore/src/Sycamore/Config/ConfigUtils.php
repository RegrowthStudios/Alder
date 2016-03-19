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

    namespace Sycamore\Config;
    
    use Sycamore\Config\Exception\InvalidConfigException;
    use Sycamore\Stdlib\ArrayUtils;
    
    use Zend\Config\Config as ZendConfig;
    use Zend\Config\Writer\PhpArray;
    
    /**
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016 Matthew Marshall
     */
    class ConfigUtils
    {
        /**
         * Saves the given config object to the given filename.
         * 
         * @param string $filename The filename at which to save the config to.
         * @param array|\Traversable|\Zend\Config\Config $config The configuration object to be saved.
         * 
         * @throws \Sycamore\Config\Exception\InvalidConfigException if the config object wasn't in an expected form.
         * @throws \InvalidArgumentException if the filename is blank.
         * @throws \RuntimeException if there was an error writing to the file.
         */
        public static function save($filename, $config)
        {
            // Construct writer.
            $writer = new PhpArray();
            
            // Get config into expected form.
            if ($config instanceof ZendConfig) {
                $config = $config->toArray();
            } else if ($config instanceof \Traversable) {
                $config = ArrayUtils::iteratorToArray($config);
            } else if (!is_array($config)) {
                throw new InvalidConfigException("The config was not in any of the expected forms; iterator, array or Zend Config object.");
            }
            
            // Save config to given file.
            try {
                $writer->toFile($filename, $config);
            } catch (\InvalidArgumentException $invArgEx) {
                throw $invArgEx;
            } catch (\RuntimeException $runtimeEx) {
                throw $runtimeEx;
            }
        }
    }
