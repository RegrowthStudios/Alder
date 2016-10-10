<?php

    namespace Alder\Acl;
    
    use Zend\Permissions\Acl\Acl as ZendAcl;
    
    /**
     * Provides functionality for retrieving and storing ACL data as well as checking access rights of users against it.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class Acl extends ZendAcl
    {
        /**
         * Rule type: hide
         */
        const TYPE_HIDE = "TYPE_HIDE";
        
        /**
         * Rule type: frequency of usage
         */
        const TYPE_FREQ = "TYPE_FREQ";
        
        /**
         * ACL rules; whitelist (deny everything to all) by default
         *
         * @var array
         */
        protected $rules = [
            "allResources" => [
                "allRoles" => [
                    "allPrivileges" => [
                        "type"   => self::TYPE_DENY,
                        "assert" => NULL,
                        "frequency" => NULL
                        
                    ],
                    "byPrivilegeId" => []
                ],
                "byRoleId" => []
            ],
            "byResourceId" => []
        ];
    
        /**
         * {@inheritdoc}
         */
        public function setRule($operation, $type, $roles = NULL, $resources = NULL, $privileges = NULL, \Zend\Permissions\Acl\Assertion\AssertionInterface $assert = NULL)
        {
            $type = strtoupper($type);
            if (self::TYPE_ALLOW !== $type && self::TYPE_DENY !== $type &&
                    self::TYPE_HIDE !== $type && self::TYPE_FREQ !== $type) {
                throw new \InvalidArgumentException(sprintf(
                    "Unsupported rule type; must be either \"%s\", \"%s\", \"%s\" or \"%s\"",
                    self::TYPE_ALLOW,
                    self::TYPE_DENY,
                    self::TYPE_HIDE,
                    self::TYPE_FREQ
                ));
            }

            $this->normaliseRoles($roles);
            
            $this->normaliseResources($resources);
            
            $this->normalisePrivileges($privileges);
            
            // Figure out how the ACL tree works.
            switch ($operation) {
                case self::OP_ADD:
                    foreach ($resources as $resource) {
                        foreach ($roles as $role) {
                            $rules = &$this->getRules($resource, $role, true);
                            if ($type !== self::TYPE_FREQ) {
                                if (count($privileges) === 0) {
                                    $rules["allPrivileges"]["type"]   = $type;
                                    $rules["allPrivileges"]["assert"] = $assert;
                                    if (!isset($rules["byPrivilegeId"])) {
                                        $rules["byPrivilegeId"] = [];
                                    }
                                } else {
                                    foreach ($privileges as $privilege) {
                                        $rules["byPrivilegeId"][$privilege]["type"]   = $type;
                                        $rules["byPrivilegeId"][$privilege]["assert"] = $assert;
                                    }
                                }
                            } else {
                                if (count($privileges) === 0) {
                                    if (!isset($rules["allPrivileges"]) || !isset($rules["allPrivileges"]["type"])) {
                                        $rules["allPrivileges"]["type"]   = self::TYPE_DENY;
                                        $rules["allPrivileges"]["assert"] = NULL;
                                        if (!isset($rules["byPrivilegeId"])) {
                                            $rules["byPrivilegeId"] = [];
                                        }
                                    }
                                    // Set frequency.
                                } else {
                                    foreach ($privileges as $privilege) {
                                        if (!isset($rules["byPrivilegeId"]) || !isset($rules["byPrivilegeId"][$privilege])) {
                                            $rules["byPrivilegeId"][$privilege]["type"]   = self::TYPE_DENY;
                                            $rules["byPrivilegeId"][$privilege]["assert"] = NULL;
                                        }
                                        // Set frequency.
                                    }
                                }
                            }
                        }
                    }
                    break;
                case self::OP_REMOVE:
                    
            }
        }
        
        /**
         * Ensure that all specified Roles exist; normalise input to array of Role objects or null.
         * 
         * @param Role\RoleInterface|string|array $roles
         */
        protected function normaliseRoles(& $roles)
        {
            if (!is_array($roles)) {
                $roles = [$roles];
            } else if (count($roles) === 0) {
                $roles = [NULL];
            }
            $rolesTemp = $roles;
            $roles = [];
            foreach ($rolesTemp as $role) {
                if ($role !== NULL) {
                    $roles[] = $this->getRoleRegistry()->get($role);
                } else {
                    $roles[] = NULL;
                }
            }
            unset($rolesTemp);
        }
        
        /**
         * Ensure that all specified Resources exist; normalise input to array of Resource objects or null
         * 
         * @param Resource\ResourceInterface|string|array $resources
         */
        protected function normaliseResources(& $resources)
        {
            if (!is_array($resources)) {
                if ($resources === NULL && count($this->resources) > 0) {
                    $resources = array_keys($this->resources);
                    // Passing a null resource; make sure "global" permission is also set!
                    if (!in_array(NULL, $resources)) {
                        array_unshift($resources, NULL);
                    }
                } else {
                    $resources = [$resources];
                }
            } else if (count($resources) === 0) {
                $resources = [NULL];
            }
            $resourcesTemp = $resources;
            $resources = [];
            foreach ($resourcesTemp as $resource) {
                if ($resource !== NULL) {
                    $resourceObj = $this->getResource($resource);
                    $resourceId = $resourceObj->getResourceId();
                    $children = $this->getChildResources($resourceObj);
                    $resources = array_merge($resources, $children);
                    $resources[$resourceId] = $resourceObj;
                } else {
                    $resources[] = NULL;
                }
            }
            unset($resourcesTemp);
        }
        
        /**
         * Normalises privileges into an array.
         * 
         * @param string|array $privileges
         */
        protected function normalisePrivileges(& $privileges)
        {
            if ($privileges === NULL) {
                $privileges = [];
            } else if (!is_array($privileges)) {
                $privileges = [$privileges];
            }
        }
    }