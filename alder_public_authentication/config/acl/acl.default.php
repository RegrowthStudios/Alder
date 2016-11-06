<?php
    
    use Zend\Permissions\Acl\Acl;
    use Zend\Permissions\Acl\Resource\GenericResource as Resource;
    use Zend\Permissions\Acl\Role\GenericRole as Role;
    
    // TODO(Matthew): Custom roll of ACL object to allow for per role, per resource, per privilege restrictions beyond just allow/deny (e.g. frequency of usage, hide etc.)?
    
    $acl = new Acl();
    
    $acl->addRole(new Role(GUEST));
    $acl->addRole(new Role(REGISTERED));
    $acl->addRole(new Role(MODERATOR),
                  REGISTERED); // Moderator has no extra actions performable beyond a regular member in authentication service.
    $acl->addRole(new Role(ADMIN));
    $acl->addRole(new Role(SUPER_ADMIN));
    
    $acl->addResource(new Resource(AUTHENTICATE));
    $acl->addResource(new Resource(LICENSE));
    $acl->addResource(new Resource(LICENSE_TEXT));
    $acl->addResource(new Resource(USER));
    // Maps shouldn't be end points?
    // Would just be seen as change to user or license.
    //$acl->addResource(new Resource(LICENSE_LICENSE_TEXT_MAP));
    //$acl->addResource(new Resource(USER_LICENSE_MAP));
    
    // Allow access to all endpoints' OPTIONS and GET requests.
    $acl->allow(null, null, [GET, OPTIONS]);
    
    // Allow admins and super admins to create, delete, modify and replace resources.
    $acl->allow([ADMIN, SUPER_ADMIN], null, [CREATE, DELETE, REPLACE, UPDATE]);
    
    // Allow guests, in addition to admins and super admins, access to create users and user sessions.
    $acl->allow([GUEST], [USER, AUTHENTICATE], [CREATE]);
    
    // Allow registered to delete and modify users (themselves).
    $acl->allow([REGISTERED], [USER], [DELETE, UPDATE]);
    
    return $acl;
