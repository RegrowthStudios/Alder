<?php

    namespace Alder\Db\Row;

    use Alder\Db\Row\AbstractRow;

    /**
     * Representation of a row in the table of licenses.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class License extends AbstractRow
    {
        public $id;
        public $name;
        public $description;
        public $product_id;
        public $simultaneous_usage_count;
    }
