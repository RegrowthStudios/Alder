<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\ScopeAudit as ScopeAuditTable;
    
    /**
     * Representation of a row in the table of scope audits.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ScopeAudit extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  INT(11)        |  Yes  |       |       |  The ID of the scope.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the scope.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the scope.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the scope.
         * label                      |  VARCHAR(50)    |       |       |       |  The name of the scope.
         * owner_id                   |  INT(11)        |       |  Yes  |  2,1  |  The ID of the scope's owner.
         * editor_id                  |  INT(11)        |       |       |       |  The ID of the user that made the change represented by this audit instance.
         * editor_ip                  |  VARCHAR(16)    |       |       |       |  The IP of the user that made the change at the time in its packed in_addr representation.
         * editor_action              |  ENUM(...)      |       |       |       |  The action taken by the editor.
         * last_etag                  |  VARCHAR(15)    |       |  Yes  |       |  The ETag of the scope in its last instance.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = ScopeAuditTable::NAME;
        
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
