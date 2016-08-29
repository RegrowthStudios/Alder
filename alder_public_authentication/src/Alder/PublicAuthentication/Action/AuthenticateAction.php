<?php

    namespace Alder\PublicAuthentication\Action;
    
    use Alder\Action\AbstractRestfulAction;
    use Alder\Container;
    use Alder\Error\Container as ErrorContainer;
    use Alder\PublicAuthentication\User\SessionFactory;
    use Alder\PublicAuthentication\User\Security;
    use Alder\PublicAuthentication\User\Validation;

    use Zend\Diactoros\Response\JsonResponse;
    
    /**
     * The user action middleware for Alder's public authentication service.
     * Handles user-entity actions based on request and session information.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
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
         */
        protected function create()
        {
            // Get data passed in through request.
            $password = $this->getParameter("password");
            $username = $this->getParameter("username");
            $email = $this->getParameter("email");
            $extended = $this->getParameter("extended_session", false);

            // Get error container.
            $errorContainer = ErrorContainer::getInstance();

            // Assert that needed data was provided.
            if (!$password) {
                $errorContainer->addError(101010101);
            }
            if (!$username && !$email) {
                $errorContainer->addError(101010102);
            }
            if ($errorContainer->hasErrors()) {
                $this->response = new JsonResponse([
                    "errors" => $errorContainer->retrieveErrors()
                ], 400);
                return;
            }

            // Assert that the username or email, as provided, are valid.
            if (!$username && !Validation::isEmail($email)) {
                $this->response = new JsonResponse([
                    "errors" => [
                        101010103 => $errorContainer->retrieveErrorString(101010103)
                    ]
                ], 400);
                return;
            } else if ($username && !Validation::isUsername($username)) {
                $this->response = new JsonResponse([
                    "errors" => [
                        101010104 => $errorContainer->retrieveErrorString(101010104)
                    ]
                ], 400);
                return;
            }

            // Acquire the user table.
            /**
             * @var \Alder\Db\Table\User $userTable
             */
            $userTable = Container::get()->get("AlderTableCache")->fetchTable("User");

            // Acquire the user authenticating from the database.
            /**
             * @var \Alder\Db\Row\User $user
             */
            $user = NULL;
            if ($username) {
                $user = $userTable->getByUsername($username);
            } else {
                $emailParts = explode("@", $email);
                $user = $userTable->getByEmail($emailParts[0], $emailParts[1]);
            }

            // If the user does not exist, fail.
            if (!$user) {
                $this->response = new JsonResponse([
                    "errors" => [
                        101010105 => $errorContainer->retrieveErrorString(101010105)
                    ]
                ], 400);
                return;
            }

            // Assert password provided is correct.
            if (Security::verifyPassword($password, $user->password_hash)) {
                // Update password hash if hashing standards have changed.
                if (Security::passwordNeedsRehash($user->password_hash)) {
                    $user->password_hash = UserUtils::hashPassword($password);
                    $userTable->updateRow($user, [ "id" => $user->id ]);
                }

                // Create session token.
                $sessionToken = SessionFactory::create($user->id, $user->toArray(), $extended);

                // If token failed to be generated, fail.
                if (!$sessionToken) {
                    $this->response = new JsonResponse([
                        "errors" => [
                            101010106 => $errorContainer->retrieveErrorString(101010106)
                        ]
                    ], 400);
                    return;
                }

                // Acquire configuration object.
                $config = Container::get()->get("config")["alder"];

                // Acquire the correct session length.
                if (!$extended) {
                    $sessionLength = $config["public_authentication"]["session"]["duration"];
                } else {
                    $sessionLength = $config["public_authentication"]["session"]["duration_extended"];
                }

                // Attempt to set the cookie, fail on false return.
                $this->response = new JsonResponse([
                    "session" => [
                        "token" => $sessionToken,
                        "duration" => time() + $sessionLength,
                        "domain" => $config["domain"],
                        "https_only_flag" => $config["security"]["cookies_over_https_only"],
                        "access_by_http_only_flag" => $config["security"]["access_cookies_via_http_only"]
                    ]
                ], 200);
                return;
            }

            // Fail due to invalid password.
            $this->response = new JsonResponse([
                "errors" => [
                    101010107 => $errorContainer->retrieveErrorString(101010107)
                ]
            ], 400);
            return;
        }
    }
