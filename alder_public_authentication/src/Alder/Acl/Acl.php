<?php

    namespace Alder\Acl;
    
    use Zend\Permissions\Acl\Acl as ZendAcl;
    use Zend\Permissions\Acl\Assertion\AssertionInterface;
    
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
        
//        /**
//         * Rule type: frequency of usage
//         */
//        const TYPE_FREQ = "TYPE_FREQ";
        
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
         * Adds a "hide" rule to the ACL
         *
         * @param  Role\RoleInterface|string|array          $roles
         * @param  Resource\ResourceInterface|string|array  $resources
         * @param  string|array                             $privileges
         * @param  Assertion\AssertionInterface             $assert
         * @return Acl Provides a fluent interface
         */
        public function hide($roles = NULL, $resources = NULL, $privileges = NULL, AssertionInterface $assert = NULL)
        {
            return $this->setRule(self::OP_ADD, self::TYPE_HIDE, $roles, $resources, $privileges, $assert);
        }

        /**
         * Removes "hide" restrictions from the ACL
         *
         * @param  Role\RoleInterface|string|array         $roles
         * @param  Resource\ResourceInterface|string|array $resources
         * @param  string|array                            $privileges
         * @return Acl Provides a fluent interface
         */
        public function removeHide($roles = NULL, $resources = NULL, $privileges = NULL)
        {
            return $this->setRule(self::OP_REMOVE, self::TYPE_HIDE, $roles, $resources, $privileges);
        }
        
        /**
         * {@inheritdoc}
         */
        public function setRule($operation, $type, $roles = NULL, $resources = NULL, $privileges = NULL, AssertionInterface $assert = NULL)
        {
            $type = strtoupper($type);
            if (self::TYPE_ALLOW !== $type && self::TYPE_DENY !== $type &&
                    self::TYPE_HIDE !== $type /*&& self::TYPE_FREQ !== $type*/) {
                throw new \InvalidArgumentException(sprintf(
                    "Unsupported rule type; must be either \"%s\", \"%s\" or \"%s\"",
                    self::TYPE_ALLOW,
                    self::TYPE_DENY,
                    self::TYPE_HIDE
                ));
            }

            $this->normaliseRoles($roles);
            
            $this->normaliseResources($resources);
            
            $this->normalisePrivileges($privileges);
            
            switch ($operation) {
                case self::OP_ADD:
                    $this->applyAddOperation($type, $roles, $resources, $privileges, $assert);
                    break;

                case self::OP_REMOVE:
                    $this->applyRemoveOperation($type, $roles, $resources, $privileges);
                    break;
                
                default:
                    throw new \InvalidArgumentException(sprintf(
                        "Unsupported rule type; must be either \"%s\", \"%s\" or \"%s\"",
                        self::TYPE_ALLOW,
                        self::TYPE_DENY,
                        self::TYPE_HIDE
                    ));
            }

            return $this;
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
        
        /**
         * Apply the add operation for a given rule type on a given set of rules, resources and privileges.
         * 
         * @param string $type
         * @param Role\RoleInterface|string|array $roles
         * @param Resource\ResourceInterface|string|array $resources
         * @param string|array $privileges
         * @param \Zend\Permissions\Acl\Assertion\AssertionInterface $assert
         */
        protected function applyAddOperation($type, $roles, $resources, $privileges, AssertionInterface $assert)
        {
            foreach ($resources as $resource) {
                foreach ($roles as $role) {
                    $rules =& $this->getRules($resource, $role, true);
                    if (count($privileges) === 0) {
                        $rules['allPrivileges']['type']   = $type;
                        $rules['allPrivileges']['assert'] = $assert;
                        if (!isset($rules['byPrivilegeId'])) {
                            $rules['byPrivilegeId'] = [];
                        }
                    } else {
                        foreach ($privileges as $privilege) {
                            $rules['byPrivilegeId'][$privilege]['type']   = $type;
                            $rules['byPrivilegeId'][$privilege]['assert'] = $assert;
                        }
                    }
                }
            }
        }
        
        /**
         * Apply the remove operation for a given rule type on a given set of rules, resources and privileges.
         * 
         * @param string $type
         * @param Role\RoleInterface|string|array $roles
         * @param Resource\ResourceInterface|string|array $resources
         * @param string|array $privileges
         */
        protected function applyRemoveOperation($type, $roles, $resources, $privileges)
        {
            foreach ($resources as $resource) {
                foreach ($roles as $role) {
                    $rules =& $this->getRules($resource, $role);
                    if (null === $rules) {
                        continue;
                    }
                    if (count($privileges) === 0) {
                        if ($resource === NULL && $role === NULL) {
                            if ($type === $rules['allPrivileges']['type']) {
                                $rules = [
                                    'allPrivileges' => [
                                        'type'   => self::TYPE_DENY,
                                        'assert' => NULL
                                    ],
                                    'byPrivilegeId' => []
                                ];
                            }
                            continue;
                        }

                        if (isset($rules['allPrivileges']['type']) && $type === $rules['allPrivileges']['type']) {
                            unset($rules['allPrivileges']);
                        }
                    } else {
                        foreach ($privileges as $privilege) {
                            if (isset($rules['byPrivilegeId'][$privilege]) && $type === $rules['byPrivilegeId'][$privilege]['type']) {
                                unset($rules['byPrivilegeId'][$privilege]);
                            }
                        }
                    }
                }
            }
        }
        
        /**
         * {@inheritdoc}
         */
        public function isAllowed($role = NULL, $resource = NULL, $privilege = NULL)
        {
            // Reset role & resource to NULL.
            $this->isAllowedRole = NULL;
            $this->isAllowedResource = NULL;
            $this->isAllowedPrivilege = NULL;

            if ($role !== NULL) {
                // Keep track of originally called role.
                $this->isAllowedRole = $role;
                $role = $this->getRoleRegistry()->get($role);
                if (!$this->isAllowedRole instanceof RoleInterface) {
                    $this->isAllowedRole = $role;
                }
            }

            if ($resource !== NULL) {
                // Keep track of originally called resource
                $this->isAllowedResource = $resource;
                $resource = $this->getResource($resource);
                if (!$this->isAllowedResource instanceof ResourceInterface) {
                    $this->isAllowedResource = $resource;
                }
            }

            if ($privilege === NULL) {
                // Query on all privileges, loop terminates at 'allResources' pseudo-parent
                while (true) {
                    // Depth-first search on $role if it is not 'allRoles' pseudo-parent
                    if ($role !== NULL && ($result = $this->roleDFSAllPrivileges($role, $resource, $privilege)) !== NULL) {
                        return $result;
                    }

                    // Look for rule on 'allRoles' pseudo-parent
                    if (($rules = $this->getRules($resource, NULL)) !== NULL) {
                        foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
                            if (($ruleTypeOnePrivilege = $this->getRuleType($resource, NULL, $privilege)) !== self::TYPE_ALLOW) {
                                return false;
                            }
                        }
                        if (($ruleTypeAllPrivileges = $this->getRuleType($resource, NULL, NULL)) !== NULL) {
                            return $ruleTypeAllPrivileges === self::TYPE_ALLOW;
                        }
                    }

                    // Try next Resource
                    $resource = $this->resources[$resource->getResourceId()]['parent'];
                }
            } else {
                $this->isAllowedPrivilege = $privilege;
                // Query on one privilege, loop terminates at 'allResources' pseudo-parent                
                while (true) {
                    // Depth-first search on $role if it is not 'allRoles' pseudo-parent
                    if ($role !== NULL && ($result = $this->roleDFSOnePrivilege($role, $resource, $privilege)) !== NULL) {
                        return $result;
                    }

                    // Look for rule on 'allRoles' pseudo-parent
                    if (($ruleType = $this->getRuleType($resource, NULL, $privilege)) !== NULL) {
                        return $ruleType === self::TYPE_ALLOW;
                    } else if (($ruleTypeAllPrivileges = $this->getRuleType($resource, NULL, NULL)) !== NULL) {
                        $result = $ruleTypeAllPrivileges === self::TYPE_ALLOW;
                        if ($result || $resource === NULL) {
                            return $result;
                        }
                    }

                    // Try next Resource
                    $resource = $this->resources[$resource->getResourceId()]['parent'];
                }
            }
        }
        
        /**
         * {@inheritdoc}
         */
        protected function roleDFSVisitAllPrivileges(RoleInterface $role, ResourceInterface $resource = NULL, &$dfs = NULL)
        {
            if ($dfs === NULL) {
                throw new Exception\RuntimeException('$dfs parameter may not be null');
            }

            if (($rules = $this->getRules($resource, $role)) !== NULL) {
                foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
                    if (($ruleTypeOnePrivilege = $this->getRuleType($resource, $role, $privilege)) !== self::TYPE_ALLOW) {
                        return false;
                    }
                }
                if (($ruleTypeAllPrivileges = $this->getRuleType($resource, $role, NULL)) !== NULL) {
                    return $ruleTypeAllPrivileges === self::TYPE_ALLOW;
                }
            }

            $dfs['visited'][$role->getRoleId()] = true;
            foreach ($this->getRoleRegistry()->getParents($role) as $roleParent) {
                $dfs['stack'][] = $roleParent;
            }

            return;
        }
    }