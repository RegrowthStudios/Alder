<?php

    namespace Alder\Db\Table;
    
    use Alder\Db\Row\AbstractRowInterface;
    
    /**
     * Interface setting out contract for all row objects.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    interface AbstractTableInterface
    {
        /**
         * Gets all entries of a table from cache first, then
         * directly from database.
         * 
         * @param bool $forceDbFetch Whether to force a direct database fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched entries.
         */
        public function fetchAll($forceDbFetch = false);
        
        /**
         * Updates rows matching the given identifiers.
         * 
         * @param \Alder\Db\Row\AbstractRowInterface $row The row data with which to update matching rows.
         * @param string|array|\Closure $identifiers The identifiers that rows must be matched against.
         * 
         * @return int The number of affected rows.
         */
        public function updateRow(AbstractRowInterface $row, $identifiers = NULL);
        
        /**
         * Insert a new row to the table.
         * 
         * @param \Alder\Db\Row\AbstractRowInterface $row The row data to be inserted.
         * 
         * @return int The number of affected rows.
         */
        public function insertRow(AbstractRowInterface $row);
    }
