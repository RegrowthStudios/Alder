<?php

    namespace Alder\PublicAuthentication\Db\Row;

    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\UserLicenseMap as UserLicenseMapTable;

    /**
     * Representation of a row in the table of user license map.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class UserLicenseMap extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * user_id                    |  INT(11)        |       |  Yes  |  1,1  |  The ID of the user.
         * license_id                 |  INT(11)        |       |  Yes  |  1,1  |  The ID of the license.
         * etag                       |  VARCHAR(15)    |       |       |  2,1  |  The ETag of the user license map.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the user.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the user.
         * license_quantity           |  INT(3)         |       |       |       |  The number of the license the associated user holds.
         */

        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = UserLicenseMapTable::NAME;
        
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
