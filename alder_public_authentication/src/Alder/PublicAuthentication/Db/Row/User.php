<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\User as UserTable;
    
    /**
     * Representation of a row in the table of users.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class User extends AbstractRow
    {
        /*
         * NAME                       |  TYPE           |  PK   |  FK   |  UK   |  DESCRIPTION
         * id                         |  INT(11)        |  Yes  |       |       |  The ID of the user.
         * etag                       |  VARCHAR(15)    |       |       |  1,1  |  The ETag of the user.
         * last_change_timestamp      |  VARCHAR(11)    |       |       |       |  The timestamp of the last change made to the user.
         * creation_timestamp         |  VARCHAR(11)    |       |       |       |  The timestamp of the creation of the user.
         * username                   |  VARCHAR(32)    |       |       |       |  The username of the user.
         * primary_email_local        |  VARCHAR(64)    |       |       |       |  The local part of the primary email of the user.
         * primary_email_domain       |  VARCHAR(255)   |       |       |       |  The domain part of the primary email of the user.
         * password_hash              |  VARCHAR(255)   |       |       |       |  The hash of the user's password.
         */
        
        /**
         * The name of the table.
         *
         * @var string
         */
        protected static $table = UserTable::NAME;
        
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
            parent::__construct(self::$table);
        }
    }
