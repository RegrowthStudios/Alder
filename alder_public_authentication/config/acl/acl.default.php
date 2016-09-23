<?php

    use Zend\Permissions\Acl\Acl;
    use Zend\Permissions\Acl\Resource\GenericResource as Resource;
    use Zend\Permissions\Acl\Role\GenericRole as Role;
    
    
    $acl = new Acl();
    
    $acl->addRole(new Role(GUEST));
    $acl->addRole(new Role(REGISTERED));
    $acl->addRole(new Role(MODERATOR), REGISTERED); // Moderator has no extra actions performable beyond a regular member in authentication service.
    $acl->addRole(new Role(ADMIN));
    $acl->addRole(new Role(SUPER_ADMIN));
    
    $acl->addResource(new Resource(AUTHENTICATION));
    $acl->addResource(new Resource(LICENSE));
    $acl->addResource(new Resource(LICENSE_TEXT));
    $acl->addResource(new Resource(LICENSE_LICENSE_TEXT_MAP));
    $acl->addResource(new Resource(USER));
    $acl->addResource(new Resource(USER_LICENSE_MAP));
    
    $acl->allow(NULL, NULL, [ OPTIONS ]);
    
    $acl->allow([ GUEST, ADMIN, SUPER_ADMIN ], AUTHENTICATION, [ CREATE ]);
    
    return $acl;
