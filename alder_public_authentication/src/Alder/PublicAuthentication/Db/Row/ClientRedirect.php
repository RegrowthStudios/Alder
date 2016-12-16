<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\ClientRedirect as ClientRedirectTable;
    
    /**
     * Representation of a row in the table of client redirects.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ClientRedirect extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  INT(11)        |  Yes  |       |       |  The ID of the redirect.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the redirect.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the redirect.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the redirect.
         * client_id                  |  VARCHAR(50)    |       |       |       |  The ID of the client who registered the redirect.
         * uri                        |  VARCHAR(2083)  |       |       |       |  The URI of the redirect.
         * default                    |  TINYINT(1)     |       |       |       |  Whether this is the default redirect of the associated client.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = ClientRedirectTable::NAME;
        
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
