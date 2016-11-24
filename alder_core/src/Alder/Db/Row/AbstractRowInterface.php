<?php
    namespace Alder\Db\Row;
    
    /**
     * Interface setting out contract for all row objects.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    interface AbstractRowInterface
    {
        /**
         * Enters the data provided into the row instance, exchaning it for the old data.
         *
         * @param array|\Traversable $data The data to be exchanged into the row instance.
         *
         * @return array The old data of this row instance.
         *
         * @throws \InvalidArgumentException if data provided is not an array.
         */
        public function exchangeArray($data);
        
        /**
         * Returns the data in this row instance in array form.
         *
         * @return array The data in this row instance.
         */
        public function toArray();
    }
