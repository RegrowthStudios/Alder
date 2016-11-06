<?php
    
    namespace Alder\PublicAuthentication\Action;
    
    use Alder\Action\AbstractRestfulAction;
    use Alder\Container;
    use Alder\Error\Error;
    use Alder\Error\Stack as ErrorStack;
    use Alder\PublicAuthentication\User\SessionFactory;
    use Alder\PublicAuthentication\User\Security;
    use Alder\PublicAuthentication\User\Validation;
    
    use Zend\Diactoros\Response\JsonResponse;
    
    /**
     * The user action middleware for Alder's public authentication service.
     * Handles user-entity actions based on request and session information.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class AuthenticateAction extends AbstractRestfulAction
    {
        // TODO(Matthew): OPTIONS
        
        // TODO(Matthew): Either here or at earlier middleware, reject repeated requests from same source for same resource/action.
        /**
         * Authenticate a user with provided details, and generate a user session token
         * on successful authentication.
         *
         * Errors:
         *  101010101 - No password provided.
         *  101010102 - No email or username provided.
         *  101010103 - Invalid email format.
         *  101010104 - Invalid username format.
         *  101010105 - No user exists with data provided.
         *  101010106 - Could not generate session token.
         *  101010107 - Invalid password.
         *
         * @param mixed $data Data from request.
         */
        protected function create($data) {
            // Get data passed in through request.
            $password = isset($data["password"]) ? $data["password"] : null;
            $username = isset($data["username"]) ? $data["username"] : null;
            $email = isset($data["email"]) ? $data["email"] : null;
            $extended = isset($data["extended"]) ? $data["extended"] : false;
            
            // Get error container.
            $errorStack = new ErrorStack();
            
            // Assert that needed data was provided.
            if (!$password) {
                $errorStack->push(101010101);
            }
            if (!$username && !$email) {
                $errorStack->push(101010102);
            }
            if ($errorStack->notEmpty()) {
                $this->response = new JsonResponse(["errors" => $errorStack->retrieve()], 400);
                
                return;
            }
            
            // Assert that the username or email, as provided, are valid.
            if (!$username && !Validation::isEmail($email, $errorStack)) {
                $this->response = new JsonResponse(["errors" => [101010103 => Error::retrieveString(101010103)]], 400);
                
                return;
            } else {
                if ($username && !Validation::isUsername($username, $errorStack)) {
                    $this->response = new JsonResponse(["errors" => [101010104 => Error::retrieveString(101010104)]],
                                                       400);
                    
                    return;
                }
            }
            
            // Acquire the user table.
            /**
             * @var \Alder\PublicAuthentication\Db\Table\User $userTable
             */
            $userTable = Container::get()->get("AlderTableCache")->fetchTable("User");
            
            // Acquire the user authenticating from the database.
            /**
             * @var \Alder\PublicAuthentication\Db\Row\User $user
             */
            $user = null;
            if ($username) {
                $user = $userTable->getByUsername($username);
            } else {
                $emailParts = explode("@", $email);
                $user = $userTable->getByEmail($emailParts[0], $emailParts[1]);
            }
            
            // If the user does not exist, fail.
            if (!$user) {
                $this->response = new JsonResponse(["errors" => [101010105 => Error::retrieveString(101010105)]], 400);
                
                return;
            }
            
            // Assert password provided is correct.
            if (Security::verifyPassword($password, $user->password_hash)) {
                // Update password hash if hashing standards have changed.
                if (Security::passwordNeedsRehash($user->password_hash)) {
                    $user->password_hash = Security::hashPassword($password);
                    $userTable->updateRow($user, ["id" => $user->id]);
                }
                
                // Create session cookie.
                $cookie = SessionFactory::create($user->id, $errorStack, $user->toArray(), $extended);
                
                if ($errorStack->notEmpty()) {
                    // If token failed to be generated, send 400 code and errors.
                    $this->response = new JsonResponse(["errors" => $errorStack->retrieve()], 400);
                } else {
                    $this->response = $this->response->withAddedHeader("Set-Cookie", $cookie)->withStatus(200);
                }
            } else {
                // Fail due to invalid password.
                $this->response = new JsonResponse(["errors" => [101010106 => Error::retrieveString(101010106)]], 400);
            }
        }
    }
