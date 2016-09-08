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
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  INT(11)        |  Yes  |       |       |  The ID of the license.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the license.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the license.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the license.
         * name                       |  VARCHAR(50)    |       |       |       |  The name of the license.
         * description                |  VARCHAR(512)   |       |       |       |  The description of the license.
         * product_id                 |  INT(11)        |       |       |       |  The associated product ID.
         * simultaneous_usage_count   |  TINYINT(1)     |       |       |       |  The number of simultaneous usages that may be made of the license.
         */

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
