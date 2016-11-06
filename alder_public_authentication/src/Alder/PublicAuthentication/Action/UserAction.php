<?php
    
    namespace Alder\PublicAuthentication\Action;
    
    use Alder\Action\AbstractRestfulAction;
    use Alder\Container;
    use Alder\Error\Container as ErrorContainer;
    use Alder\PublicAuthentication\Db\Table\User;
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
    class UserAction extends AbstractRestfulAction
    {
        // TODO(Matthew): DELETE, OPTIONS, HEAD
        // TODO(Matthew): ETags, conditionals etc. in headers. HANDLE THEM!
        // TODO(Matthew): Requestee authentication.
        
        protected function get($data) : void {
            /**
             * @var \Alder\Db\Table\User $userTable
             */
            $userTable = Container::get()->get("AlderTableCache")->fetchTable("User");
            
            $result = [];
            $missingContent = [];
            
            if (!is_array($data)) {
                if (!$this->getUserFromData($data, $result, $userTable)) {
                    $this->response = new JsonResponse(["errors" => [101030101 => ErrorContainer::getInstance()
                                                                                                ->retrieveErrorString(101030101)]],
                                                       400);
                    
                    return;
                }
            } else {
                foreach ($data as $datum) {
                    if (!$this->getUserFromData($datum, $result, $userTable)) {
                        $missingContent[] = $datum;
                    }
                }
                if (empty($result)) {
                    $this->response = new JsonResponse(["errors" => [101030101 => ErrorContainer::getInstance()
                                                                                                ->retrieveErrorString(101030101)]],
                                                       400);
                    
                    return;
                }
            }
            
            // TODO(MATTHEW): Determine a more full-fledged response specification for actions. Aim for the specification to be as reusable as possible.
            //                E.g. "request-type", "etag", various flags passed in with the request etc.
            
            if (empty($missingContent)) {
                $this->response = new JsonResponse(["data" => $result], 200);
            } else {
                $this->response = new JsonResponse([["status" => 200, "data" => $result],
                                                    ["status" => 400, "data" => $missingContent]], 207);
            }
        }
        
        // TODO(Matthew): Redo: need separate error stack per resource as well as provide URL of each created resource.
        protected function create($data) : void {
            $errorContainer = ErrorContainer::getInstance();
            
            if (!is_array($data)) {
                $this->response = new JsonResponse(["errors" => [101030201 => $errorContainer->retrieveErrorString(101030201)]],
                                                   400);
                
                return;
            }
            
            /**
             * @var \Alder\Db\Table\User $userTable
             */
            $userTable = Container::get()->get("AlderTableCache")->fetchTable("User");
            
            $successes = 0;
            $failures = ["unnamed" => 0, "named" => []];
            
            if (!is_array($data[0])) {
                if (!$this->createUserFromData($data, $userTable, $errorContainer)) {
                    $errorContainer->addError(101030202);
                    $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrors()], 400);
                    
                    return;
                }
            } else {
                foreach ($data as $datum) {
                    if (!is_array($datum)) {
                        $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrorString(101030203)],
                                                           400);
                        
                        return;
                    }
                }
                foreach ($data as $datum) {
                    if (!$this->createUserFromData($datum, $userTable, $errorContainer)) {
                        if (isset($datum["username"])) {
                            $failures["named"][] = $datum["username"];
                        } else {
                            ++$failures["unnamed"];
                        }
                    } else {
                        ++$successes;
                    }
                }
                if ($successes == 0) {
                    $errorContainer->addError(101030202);
                    $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrors()], 400);
                    
                    return;
                }
            }
            
            $errorContainer->addError(101030202);
            $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrors()], 400);
        }
        
        public function update($data) : void {
            //TODO(Matthew): Research patching in HTTP/1.1 specification.
        }
        
        public function replace($data) : void {
            $errorContainer = ErrorContainer::getInstance();
            
            if (!is_array($data)) {
                $this->response = new JsonResponse(["errors" => [101030401 => $errorContainer->retrieveErrorString(101030401)]],
                                                   400);
                
                return;
            }
            
            /**
             * @var \Alder\Db\Table\User $userTable
             */
            $userTable = Container::get()->get("AlderTableCache")->fetchTable("User");
            
            if (!is_array($data[0])) {
                if (!$this->replaceUserFromData($data, $userTable, $errorContainer)) {
                    $errorContainer->addError(101030402);
                    $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrors()], 400);
                    
                    return;
                }
            } else {
                foreach ($data as $datum) {
                    if (!is_array($datum)) {
                        $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrorString(101030403)],
                                                           400);
                        
                        return;
                    }
                }
                foreach ($data as $datum) {
                    if (!$this->replaceUserFromData($datum, $userTable, $errorContainer)) {
                        $errorContainer->addError(101030402);
                        $this->response = new JsonResponse(["errors" => $errorContainer->retrieveErrors()], 400);
                        
                        return;
                    }
                }
            }
        }
        
        /**
         * Gets a user identified by the data point provided.
         *
         * @param mixed                $identifier The identifier for the user desired.
         * @param array                &$result    The results array in which to dump retrieved user.
         * @param \Alder\Db\Table\User $userTable
         *
         * @return bool
         */
        protected function getUserFromData($identifier, array &$result, User $userTable) : bool {
            if (is_numeric($identifier)) {
                $user = $userTable->getById($identifier);
                if ($user) {
                    $result[] = $user->toArray();
                    
                    return true;
                }
                
                return false;
            } else {
                if (Validation::isEmail($identifier)) {
                    $user = $userTable->getByEmail(...explode("@", $identifier));
                    if ($user) {
                        $result[] = $user->toArray();
                        
                        return true;
                    }
                    
                    return false;
                } else {
                    $user = $userTable->getByUsername($identifier);
                    if ($user) {
                        $result[] = $user->toArray();
                        
                        return true;
                    }
                    
                    return false;
                }
            }
        }
        
        protected function validateUserData($data, ErrorContainer& $errorContainer) : bool {
            if (!isset($data["username"])) {
                $errorContainer->addError(101030901);
            }
            if (!isset($data["email"])) {
                $errorContainer->addError(101030902);
            }
            if (!isset($data["password"])) {
                $errorContainer->addError(101030903);
            }
            if ($errorContainer->hasErrors()) {
                return false;
            }
            
            if (!Validation::validateUsername($data["username"])
                || !Validation::validateEmail($data["email"])
                || !Validation::validatePassword($data["password"])
            ) {
                return false;
            }
        }
        
        /**
         * Creates a user with the data provided.
         *
         * @param array                  $data            The identifier for the user desired.
         * @param \Alder\Db\Table\User   &$userTable      The instance of the table to use.
         * @param \Alder\Error\Container &$errorContainer The error container to inject errors into.
         *
         * @return bool
         */
        protected function createUserFromData(array $data, UserTable& $userTable,
                                              ErrorContainer& $errorContainer) : bool {
            if (!$this->validateUserData($data, $errorContainer)) {
                return false;
            }
            
            $user = new UserRow();
            $user->username = $data["username"];
            $userEmail = explode("@", $data["email"]);
            $user->primary_email_local = $userEmail[0];
            $user->primary_email_domain = $userEmail[1];
            $user->password_hash = Security::hashPassword($data["password"]);
            /*******************************************************************\
             * URGENT
             * // TODO(Matthew): Better employee flag checking. Also authenticate the requestee as being appropriately permitted.
             * \*******************************************************************/
            $user->employee_flag = (isset($data["employee_flag"])
                                    && $data["employee_flag"] instanceof bool) ? $data["employee_flag"] : false;
            
            if (!$userTable->insertRow($user)) {
                $errorContainer->addError(101031001);
                
                return false;
            }
            
            return true;
        }
        
        /**
         * Replaces a user using the data provided.
         *
         * @param array                  $data
         * @param \Alder\Db\Table\User   &$userTable
         * @param \Alder\Error\Container &$errorContainer
         *
         * @return bool True on success, false on failure.
         */
        protected function replaceUserFromData(array $data, UserTable& $userTable,
                                               ErrorContainer& $errorContainer) : bool {
            return false;
        }
    }
