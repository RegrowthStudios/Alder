<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\Scope as ScopeTable;
    
    /**
     * Representation of a row in the table of scopes.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class Scope extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  INT(11)        |  Yes  |       |       |  The ID of the scope.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the scope.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the scope.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the scope.
         * label                      |  VARCHAR(50)    |       |       |       |  The name of the scope.
         * owner_id                   |  INT(11)        |       |  Yes  |  2,1  |  The ID of the scope's owner.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = ScopeTable::NAME;
        
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
