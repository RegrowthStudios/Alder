<?php
    namespace Sycamore\Db\Row;
    
    use Sycamore\Db\Row\AbstractRow;
    
    /**
     * Object-specific abstract row representation class with fields for object rows.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @abstract
     */
    abstract class AbstractObjectRow extends AbstractRow
    {
        // Uneditable in API:
        public $id;
        public $creationTime;
        public $creatorId;
        public $lastUpdateTime;
        public $lastUpdatorId;
    }
