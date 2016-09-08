<?php

    namespace Alder\PublicAuthentication\Db\Row;

    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\License as LicenseTable;

    /**
     * Representation of a row in the table of licenses.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class License extends AbstractRow
    {
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = LicenseTable::NAME;
        
        /**
         * The columns of the unique keys of the table.
         * 
         * @var array
         */
        protected static $uniqueKeyColumns = NULL;
        
        /**
         * The columns of the primary key of the table.
         * 
         * @var array
         */
        protected static $primaryKeyColumns = NULL;
        
        /**
         * Prepare the row.
         */
        public function __construct()
        {
            parent::__construct(static::$table);
        }
    }
