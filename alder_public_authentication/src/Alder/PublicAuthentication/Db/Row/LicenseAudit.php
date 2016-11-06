<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\LicenseAudit as LicenseAuditTable;
    
    /**
     * Representation of a row in the table of license audits.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class LicenseAudit extends AbstractRow
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
         * editor_id                  |  INT(11)        |       |       |       |  The ID of the user that made the change represented by this audit instance.
         * editor_ip                  |  VARCHAR(16)    |       |       |       |  The IP of the user that made the change at the time in its packed in_addr representation.
         * editor_action              |  ENUM(...)      |       |       |       |  The action taken by the editor.
         * last_etag                  |  VARCHAR(15)    |       |  Yes  |       |  The ETag of the license in its last instance.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = LicenseAuditTable::NAME;
        
        /**
         * The columns of the unique keys of the table.
         *
         * @var array
         */
        protected static $uniqueKeyColumns = null;
        
        /**
         * The columns of the primary key of the table.
         *
         * @var array
         */
        protected static $primaryKeyColumns = null;
        
        /**
         * Prepare the row.
         */
        public function __construct() {
            parent::__construct(static::$table);
        }
    }
