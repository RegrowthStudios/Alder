<?php
    namespace Sycamore\Db\Row;
    
    use Sycamore\Db\Row\AbstractObjectRow;
    
    /**
     * Row object representing ban table rows.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Ban extends AbstractObjectRow
    {
        // Admin Token Required:
        ///* 1 = active, 0 = lifted
        public $status;
        // Uneditable in API:
        public $expiryTime;
        public $bannedId;
    }
    