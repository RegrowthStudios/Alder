<?php
    
    namespace Alder\PublicAuthentication\Db\Row;
    
    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\User as UserTable;
    use Alder\PublicAuthentication\User\Password;
    
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
    
        /**
         * Determines if the provided password is valid for the user represented by this row. It will also
         * update the hash of the password stored in the database if the password policy has changed since the
         * last hash.
         *
         * @param string $password The password to validate.
         *
         * @return bool True if the password is valid, false otherwise.
         * @throws \Exception Thrown if a row object has not been initialised before this function is called.
         */
        public function passwordValid(string $password) : bool {
            // If the row isn't initialised or populated, row is being used wrong.
            if (!$this->isInitialized || !isset($this->data["password_hash"]) || !isset($this->data["id"])) {
                throw new \Exception("Row has not been initialised or populated, cannot verify a password for a null user.");
            }
            
            // Verify password, if valid check if the password hash needs updating according to most current password policy.
            if (Password::verify($password, $this->data["password_hash"])) {
                if (Password::needsRehash($this->data["password_hash"])) {
                    $this->data["password_hash"] = Password::hash($password);
                    $this->save();
                }
                return true;
            }
            return false;
        }
    }
