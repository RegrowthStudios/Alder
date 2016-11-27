<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\ClientScopeMap as ClientScopeMapTable;
    
    /**
     * Representation of a row in the table of client scope map.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ClientScopeMap extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * client_id                  |  INT(11)        |       |  Yes  |  1,1  |  The ID of the client.
         * scope_id                   |  INT(11)        |       |  Yes  |  1,1  |  The ID of the scope.
         * etag                       |  VARCHAR(15)    |       |       |  2,1  |  The ETag of the client scope map.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the map entry.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the map entry.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = ClientScopeMapTable::NAME;
        
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
