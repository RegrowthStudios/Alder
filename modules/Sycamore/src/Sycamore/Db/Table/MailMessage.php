<?php
    namespace Sycamore\Db\Table;
    
    use Sycamore\Db\Row\MailMessage as MailMessageRow;
    use Sycamore\Db\Table\AbstractObjectTable;

    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Table representation class for mail messages.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class MailMessage extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager of this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            parent::__construct($serviceManager, "mail_messages", new MailMessageRow());
        }
        
        /**
         * Gets mail messages by their sent status.
         * 
         * @param bool $sent The sent state to fetch against.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getBySent($sent, $forceDbFetch = false)
        {
            return $this->getByKey("sent", $sent, $forceDbFetch);
        }
        
        /**
         * Gets mail messages by their cancellation status.
         * 
         * @param bool $cancelled The cancellation state to fetch against.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getByCancelled($cancelled, $forceDbFetch = false)
        {
            return $this->getByKey("cancelled", $cancelled, $forceDbFetch);
        }
        
        /**
         * Gets mail messages by their purpose.
         * 
         * @param string $purpose The purpose of the mail messages to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getByPurpose($purpose, $forceDbFetch = false)
        {
            return $this->getByKey("purpose", $purpose, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent after a certain time.
         * 
         * @param int $sendTimeMin The minimum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getBySendTimeMin($sendTimeMin, $forceDbFetch = false)
        {
            return $this->getByKeyGreaterThanOrEqualTo("sendTime", $sendTimeMin, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent before a certain time.
         * 
         * @param int $sendTimeMax The maximum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getBySendTimeMax($sendTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyLessThanOrEqualTo("sendTime", $sendTimeMax, $forceDbFetch);
        }
        
        /**
         * Gets mail messages to be sent within a time range.
         * 
         * @param int $sendTimeMin The minimum time that a mail message should be sent at.
         * @param int $sendTimeMax The maximum time that a mail message should be sent at.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return Zend\Db\ResultSet\ResultSet The set of fetched mail messages.
         */
        public function getBySendTimeRange($sendTimeMin, $sendTimeMax, $forceDbFetch = false)
        {
            return $this->getByKeyBetween("sendTime", $sendTimeMin, $sendTimeMax, $forceDbFetch);
        }
    }
