<?php
    
    namespace Alder\Middleware;
    
    use Alder\DiContainer;
    
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    
    use Zend\Diactoros\Response\JsonResponse;
    use Zend\Stratigility\MiddlewareInterface;
    
    /**
     * The authorisation middleware for parsing authorisation details in headers.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class AuthorisationMiddleware implements MiddlewareInterface
    {
        public function __invoke(ServerRequestInterface $request, ResponseInterface $response,
                                 callable $next = null) : ResponseInterface {
            // Fetch translator object.
            /**
             * @var \Zend\I18n\Translator\Translator $translator
             */
            $translator = DiContainer::get()->get("translator");
            
            // Acquire the Authorization header if set, if not set skip this middleware.
            if (!empty($authHeader = $request->getHeader("Authorization"))) {
                // Ensure only one Authorization header value was set.
                if (count($authHeader) === 1) {
                    // Ensure the value set is decode-able.
                    if ($decodedStr = base64_decode($authHeader[0])) {
                        // Ensure the value set is a user:pass string.
                        if (count($authParts = explode(":", $decodedStr)) === 2) {
                            // All is good, set the authorisation details as an attribute of the request and hand off to next middleware.
                            $request = $request->withAttribute("authorisation", [ "id" => $authParts[0], "secret" => $authParts[1] ]);
                            
                            return $next($request, $response);
                        }
                    }
                }
    
                // If we get here then something's wrong with the Authorization header, blame the requestee.
                return new JsonResponse([
                    "error" => "invalid_authorisation_header",
                    "error_description" => $translator->translate("invalid_authorisation_header")
                ]);
            }
            
            // No Authorization header was set, so just hand on to next middleware.
            return $next($request, $response);
        }
    }
