<?php

    namespace Alder\PublicAuthentication\Middleware;
    
    use Lcobucci\JWT\Builder;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The session middleware for Alder's public authentication service.
     * Determines if a visitor already has a session JWT and if so packs the data into 
     * the request message.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class SessionMiddleware implements MiddlewareInterface
    {
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $out = null)
        {
            $alis = $request->getCookieParams()["alis"];
            
            
        }
    }
