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
    class MailMessage extends AbstractObjectRow
    {
        // Admin Token Required:
        public $serialisedMessage;
        public $sendTime;
        public $purpose;
        public $cancelled;
        public $task;
        // Uneditable in API:
        public $sent;
    }
