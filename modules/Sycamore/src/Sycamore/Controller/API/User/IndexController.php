<?php
    namespace Sycamore\Controller\API\User;
    
    use Sycamore\AbstractRestfulController;
    
    /**
     * Controller for getting, creating, editing and deleting users.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     */
    class IndexController extends AbstractRestfulController
    {
        /**
         * Fetches users matching the request parameters.
         * 
         * @param int $id The ID of the user to fetch if provided.
         * 
         * @return \Zend\Stdlib\ResponseInterface The resulting response after processing the request.
         */
        public function get($id = NULL)
        {
            $config = $this->serviceLocator->get("config");
            $this->response->setContent(json_encode(["result" => $config["Sycamore"]]));
            return $this->response;
        }
    }
