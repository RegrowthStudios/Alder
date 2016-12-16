<?php
    
    namespace Alder\PublicAuthentication\Db\Table;
    
    use Alder\Db\Table\AbstractTable;
    use Alder\PublicAuthentication\Db\Row\User as UserRow;
    
    /**
     * Gateway for the user table.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class User extends AbstractTable
    {
        /**
         * @var string Name of the table.
         */
        const NAME = "users";
        
        /**
         * Prepare the user table gateway, establishing the row prototype.
         */
        public function __construct() {
            parent::__construct(self::NAME, new UserRow());
        }
    
        /**
         * Gets a user by their username or email, whichever is provided.
         *
         * @param string $usernameOrEmail
         *
         * @return \Alder\PublicAuthentication\Db\Row\User The fetched user, NULL if no matches found.
         */
        public function getByUsernameOrEmail(string $usernameOrEmail) : UserRow {
            if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
                return $this->getByCompositeUniqueKey(["primary_email_local", "primary_email_domain"], explode("@", $usernameOrEmail), ["id", "password_hash"]);
            } else {
                return $this->getByUniqueKey("username", $usernameOrEmail. ["id", "password_hash"]);
            }
        }
    }
