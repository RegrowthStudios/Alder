<?php
    namespace Sycamore\Db\Table;
    
    use Sycamore\Db\Row\NewsletterSubscriber as NewsletterSubscriberRow;
    use Sycamore\Db\Table\AbstractObjectTable;

    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Table representation class for newsletter subscribers.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class NewsletterSubscriber extends AbstractObjectTable
    {
        /**
         * Sets up the result set prototype and then created the table gateway.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager of this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            parent::__construct($serviceManager, "newsletter_subscribers", new NewsletterSubscriberRow());
        }
        
        /**
         * Gets a newsletter subscriber by their email.
         * 
         * @param string $email The email of the newsletter subscriber to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Sycamore\Db\Row\NewsletterSubscriber The fetched newsletter subscriber.
         */
        public function getByEmail($email, $forceDbFetch = false)
        {
            return $this->getByUniqueKey("email", $email, $forceDbFetch);
        }
        
        /**
         * Gets the matching newsletter subscribers by their emails.
         * 
         * @param array $emails The emails of the newsletter subscribers to fetch.
         * @param bool $forceDbFetch Whether to force a db fetch.
         * 
         * @return \Zend\Db\ResultSet\ResultSet The set of fetched newsletter subscribers.
         */
        public function getByEmails($emails, $forceDbFetch = false)
        {
            return $this->getByKeyInCollection("email", $emails, $forceDbFetch);
        }
        
        /**
         * Checks if the given email is unique.
         * 
         * @param string $email The email to check for uniqueness amongst newsletter subscribers.
         * 
         * @return boolean True if given email is unique, false otherwise.
         */
        public function isEmailUnique($email)
        {
            return !$this->select(["email" => (string) $email])->current();
        }
    }