<?php
    
    namespace Alder\PublicAuthentication\Action;
    
    use Alder\DiContainer;
    use Alder\Action\AbstractRestfulAction;
    use Alder\PublicAuthentication\Client\AuthorisationCodeFactory;
    use Alder\PublicAuthentication\Client\UserAccessTokenFactory;
    use Alder\PublicAuthentication\Db\Row\Client;
    
    use Zend\Diactoros\Response\JsonResponse;
    
    /**
     * The client authentication action middleware for Alder's public authentication service.
     * Handles authentication requests from clients to acquire access tokens to perform actions on behalf of users.
     * Follows the OAuth 2.0 protocol.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ClientAuthorisationAction extends AbstractRestfulAction
    {
        protected const VALID_RESPONSE_TYPES = [
            "code",
            "token"
        ];
        
        protected function get() : void {
            self::create();
        }
        
        protected function create() : void {
            /**
             * @var \Zend\I18n\Translator\Translator $translator
             */
            $translator = DiContainer::get()->get("translator");
    
            //// TODO(Matthew): Implement official confidential client authentication.
            //// Fetch mediator secret.
            //// NOTE: This is NOT the client's secret, but the secret shared between the API server
            ////       and the end-user-facing website of the service provider running the API server.
            ////       E.g. between "api.pa.alder.com" and "www.alder.com".
            //$mediatorSecret = $this->getParameter("secret", null);
            //if (!$mediatorSecret) {
            //    // Error: No secret provided.
            //}
            //$apiSecret = DiContainer::get()->get("config")["alder"]["api_secret"];
            //if ($mediatorSecret !== $apiSecret) {
            //    // Error: Invalid secret provided.
            //}
            
            // Fetch response type, fail if it isn't valid.
            $responseType = $this->getParameter("response_type", null);
            if (!$responseType || !in_array($responseType, self::VALID_RESPONSE_TYPES)) {
                $this->response = new JsonResponse([
                    "for" => "client",
                    "error" => "invalid_request",
                    "error_description" => sprintf($translator->translate("invalid_response_type", "oauth"), join(", ", self::VALID_RESPONSE_TYPES))
                ]);
                return;
            }
            
            // Fetch client ID, fail if it isn't provided.
            $clientId = $this->getParameter("client_id", null);
            if (!$clientId) {
                $this->response = new JsonResponse([
                    "for" => "end_user",
                    "error" => "invalid_request",
                    "error_description" => $translator->translate("missing_client_id", "oauth")
                ]);
                return;
            }
            
            // Fetch redirect URI, fail if it isn't provided.
            $redirectUri = $this->getParameter("redirect_uri", null);
            
            // Fetch table cache.
            $tableCache = DiContainer::get()->get("alder_pa_table_cache");
            
            // Fetch client, fail if none match provided client ID.
            /**
             * @var \Alder\PublicAuthentication\Db\Table\Client $clientTable
             */
            $clientTable = $tableCache->fetchTable("Client");
            /**
             * @var \Alder\PublicAuthentication\Db\Row\Client $client
             */
            $client = $clientTable->getByUniqueKey("client_id", (int) $clientId);
            if (!$client) {
                $this->response = new JsonResponse([
                    "for" => "end_user",
                    "error" => "invalid_request",
                    "error_description" => $translator->translate("invalid_client_id", "oauth")
                ]);
                return;
            }
            
            // Get registered redirects of the requesting client. If no redirects are registered,
            // or if the redirect URI specified in the request is not one of those registered,
            // or if no redirect URI was specified in the request and none of the registered redirects
            //    is specified as the default redirect path, then fail.
            $clientRedirects = $client->getRegisteredRedirects();
            if (!$clientRedirects
                || ($redirectUri !== null && !in_array($redirectUri, $clientRedirects))
                || ($redirectUri === null && !isset($clientRedirects["default"]))) {
                $this->response = new JsonResponse([
                    "for" => "end_user",
                    "error" => "invalid_request",
                    "error_description" => $translator->translate("invalid_redirect_uri", "oauth")
                ]);
            }
            $redirectUri = $redirectUri ?: $clientRedirects["default"];
            
            // Fetch allowed scope of authorisation request.
            $scopes = $client->getAllowedScopes($this->getParameter("scope", Client::FULL_SCOPE));
            if ($scopes === null) {
                $this->response = new JsonResponse([
                    "for" => "end_user",
                    "error" => "server_error",
                    "error_description" => $translator->translate("server_failed")
                ], 500);
                throw new \OAuthException($translator->translate("scope_resolution_failed", "oauth"), E_USER_ERROR);
            } else if (empty($scopes)) {
                $this->response = new JsonResponse([
                    "for" => "client",
                    "error" => "access_denied",
                    "error_description" => $translator->translate("no_accessible_scopes", "oauth")
                ]);
                return;
            }
            
            // Generate whichever is requested of authorisation code or access token.
            $result = [];
            if ($responseType == "code") {
                $result["code"] = AuthorisationCodeFactory::create($clientId, $redirectUri, $scopes);
            } else {
                $result["access_token"] = UserAccessTokenFactory::create($clientId, null, $scopes);
                $result["token_type"] = "bearer";
                $result["expires_in"] = $result["access_token"]->getClaim("exp") - $result["access_token"]->getClaim("iat");
                $result["scope"] = join(" ", $scopes);
            }
            if (!$result) {
                $this->response = new JsonResponse([
                    "for" => "end_user",
                    "error" => "server_error",
                    "error_description" => $translator->translate("server_failed")
                ], 500);
                throw new \OAuthException($translator->translate("token_generation_failed", "oauth"), E_USER_ERROR);
            }
            
            // Embed result in return body.
            $this->response = new JsonResponse($result);
        }
    }
