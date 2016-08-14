<?php

    namespace Alder\PublicAuthentication\Action;
    
    use Alder\PublicAuthentication\Action\AbstractRestfulAction;
    use Alder\Stdlib\ActionUtils;

    use Zend\Diactoros\Response\JsonResponse;
    
    /**
     * The user action middleware for Alder's public authentication service.
     * Handles user-entity actions based on request and session information.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    abstract class AuthenticateAction extends AbstractRestfulAction
    {
        protected function create() {
            $password = $this->getParameter("password");
            $username = $this->getParameter("username");
            $email    = $this->getParameter("email");

            $errors = [];
            if (!$password) {
                $errors[] = 1010101;
            }
            if (!$username && !$email) {
                $errors[] = 1010102;
            }
            if (!empty($errors)) {
                $this->response = new JsonResponse([
                    "errors" => $errors
                ], 400);
                return;
            }


        }
    }
