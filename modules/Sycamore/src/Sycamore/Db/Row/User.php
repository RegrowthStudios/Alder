<?php
    namespace Sycamore\Db\Row;
    
    use Sycamore\Db\Row\AbstractObjectRow;
    
    /**
     * Row object representing user table rows.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class User extends AbstractObjectRow
    {
        // Admin/THIS User Token Required:
        public $name;
        public $preferredName;
        public $dateOfBirth;
        /// Password Required in case of THIS User Token:
        public $password;
        /// Verification Required (via JWT) in case of THIS User Token:
        public $primaryEmail;
        public $secondaryEmail;
        public $verified;
        /// Admin Action Required (DISALLOW THIS User Token - unless Admin):
        public $username;
        public $banned;
        // Uneditable in API:
        public $lastOnline;
        ///* Superusers can do anything and have complete rights (i.e. cannot be superseded by any ACL group).
        public $superUser;
    }