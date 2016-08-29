<?php

    namespace Alder\Db\Row;

    use Alder\Db\Row\AbstractRow;

    /**
     * Representation of a row in the table of user license map.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class UserLicenseMap extends AbstractRow
    {
        public $user_id;
        public $license_id;
        public $license_quantity;
    }
