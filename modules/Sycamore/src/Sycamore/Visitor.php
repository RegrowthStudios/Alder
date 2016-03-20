<?php
    namespace Sycamore;
    
    use Zend\ServiceManager\ServiceLocatorInterface;
    
    /**
     * Acquires and stores data relating to the visitor making the request for this application instance.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Visitor
    {
        /**
         * Stores data pertaining to the visitor.
         *
         * @var array
         */
        protected $data;
        
        /**
         * Grabs any existing session data if visitor is logged in and stores it.
         * 
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this application instance.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            // Grab the user session helper.
            $userSession = $serviceManager->get("Sycamore\User\Session");
            
            // Grab token payload if SLIS exists.
            $tokenPayload = [];
            if (!$userSession->acquire($tokenPayload)) {
                $this->data["isLoggedIn"] = false;
                return;
            }
            
            // Visitor is logged in, add data here.
            $this->data = array_merge($tokenPayload["applicationPayload"], [
                "isLoggedIn" => true
            ]);
        }
        
        /**
         * Returns the visitor's logged in state.
         * 
         * @return bool True if logged in, false otherwise.
         */
        public function isLoggedIn()
        {
            return $this->data["isLoggedIn"];
        }
        
        /**
         * Gets the specified property of the visitor. E.g. "id", "superUser" etc..
         * 
         * @param string $property The property to get.
         * 
         * @return mixed The value of the given property if it exists.
         * 
         * @throws \InvalidArgumentException If $property is not a valid property key.
         */
        public function get($property)
        {
            if (isset($this->data[$property])) {
                return $this->data[$property];
            }
            
            // TODO(Matthew): Add functions to fetch some common properties of the visiting user, for performance purposes?
            $fetchFunc = "fetch" . ucfirst($property);
            if (method_exists($this, $fetchFunc)) {
                $result = $this->{$fetchFunc}();
                if (!is_null($result)) {
                    $this->data[$property] = $result;
                    return $result;
                }
            }
            
            throw new \InvalidArgumentException("The property, $property, is not valid for Visitor.");
        }
    }
