<?php

    namespace Alder\PublicAuthentication\Db\Row;

    use Alder\Db\Row\AbstractRow;

    /**
     * Representation of a row in the table of users.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class User extends AbstractRow
    {
        public $id;
        public $username;
        public $primary_email_local;
        public $primary_email_domain;
        public $password_hash;
        public $license_keys;
        public $employee_flag;
    }
