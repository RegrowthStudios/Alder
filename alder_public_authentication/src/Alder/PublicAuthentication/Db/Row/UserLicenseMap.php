<?php

    namespace Alder\PublicAuthentication\Db\Row;

    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\UserLicenseMap as UserLicenseMapTable;

    /**
     * Representation of a row in the table of user license map.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class UserLicenseMap extends AbstractRow
    {
        public function __construct()
        {
            parent::__construct(UserLicenseMapTable::NAME);
        }
    }
