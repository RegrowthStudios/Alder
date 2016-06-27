<?php

    namespace Alder\PublicAuthentication\Middleware;
    
    /**
     * The cache middleware for Alder's public authentication service.
     * Determines if a request with the same parameters has already been made and cached.
     * If so passes back the cached result, if not pipes to the next middleware but catches the response
     * to cache it.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     */
    class CacheMiddleware
    {
        
    }
