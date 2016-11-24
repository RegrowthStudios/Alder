<?php
    
    namespace Alder\PublicAuthentication\Middleware;
    
    use Alder\Acl\AclContainer;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Response\JsonResponse;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The API map middleware for Alder's public authentication service.
     * Provides the API map of the service on request.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class AclMiddleware implements MiddlewareInterface
    {
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            $acl = AclContainer::create()->get();
            
            // TODO(Matthew): Determine visitor's role.
            $visitorRole = "";
            
            $action = $request->getAttribute("Zend\\Expressive\\Router\\RouteResult")->getMatchedMiddleware();
            if (!$action) {
                // Huh?
                // TODO(Matthew): Consider how this could be reached.
                // For now just erroring with server failure.
                return new JsonResponse([], 500);
            }
            $canonicalAction = canonicalise_action_class_path($action);
            
            $canonicalMethod = constant(strtoupper($request->getMethod()));
            
            if (!$acl->isAllowed($visitorRole, $canonicalAction, $canonicalMethod)) {
                return new JsonResponse([], 403); // TODO(Matthew): Provide greater context in response.
            }
            
            return $next($request, $response);
        }
    }
