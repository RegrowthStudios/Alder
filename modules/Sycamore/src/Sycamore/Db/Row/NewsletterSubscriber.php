<?php
    namespace Sycamore\Db\Row;
    
    use Sycamore\Db\Row\AbstractObjectRow;
    
    /**
     * Row object representing newsletter subscriber table rows.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class NewsletterSubscriber extends AbstractObjectRow
    {
        public $email;
        public $name;
    }