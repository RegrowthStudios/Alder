<?php

    namespace Alder\PublicAuthentication\Db\Row;

    use Alder\Db\Row\AbstractRow;
    use Alder\PublicAuthentication\Db\Table\License as LicenseTable;

    /**
     * Representation of a row in the table of licenses.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class License extends AbstractRow
    {
        public function __construct()
        {
            parent::__construct(LicenseTable::NAME);
        }
    }
