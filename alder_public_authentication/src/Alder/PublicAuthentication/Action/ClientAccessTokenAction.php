<?php
    
    namespace Alder\PublicAuthentication\Action;

    use Alder\DiContainer;
    use Alder\Action\AbstractRestfulAction;
    use Alder\Db\Table\AbstractTable;
    use Alder\PublicAuthentication\Client\ClientAccessTokenFactory;
    use Alder\PublicAuthentication\Client\UserAccessTokenFactory;
    use Alder\PublicAuthentication\Client\UserRefreshTokenFactory;
    use Alder\PublicAuthentication\Db\Row\Client;
    use Alder\Token\Validator as TokenValidator;

    use Lcobucci\JWT\Parser;
    
    use Zend\Diactoros\Response\JsonResponse;

    /**
     * The client access token action middleware for Alder's public authentication service.
     * Handles the generation of access tokens for clients on behalf of end-users.
     * Follows the OAuth 2.0 protocol.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since     0.1.0
     */
    class ClientAccessTokenAction extends AbstractRestfulAction
    {
        protected const VALID_GRANT_TYPES = [
            "authorization_code",
            "password",
            "client_credentials"
        ];
    
        /**
         * Handles the generation of access tokens for the requesting client utilising
         * the specified grant type. Packs the response into a JSON encoded body.
         */
        protected function create() : void {
            /**
             * @var \Zend\I18n\Translator\Translator $translator
             */
            $translator = DiContainer::get()->get("translator");
            
            // Fetch grant type, fail if it isn't valid.
            $grantType = $this->getParameter("grant_type", null);
            if (!$grantType || !in_array($grantType, self::VALID_GRANT_TYPES)) {
                $this->response = new JsonResponse([
                    "error" => "invalid_request",
                    "error_description" => sprintf($translator->translate("invalid_grant_type", "oauth"), join(", ", self::VALID_GRANT_TYPES))
                ], 400);
                return;
            }
            
            // Fetch client details.
            $clientId = $this->getParameter("client_id", null);
            $clientSecret = $this->getParameter("client_secret", null);
            $authHeaderDetails = $this->request->getAttribute("authorisation");
            // Fail if the client ID was not passed in either via query params or via the Authorization header.
            if (!$clientId && !$authHeaderDetails) {
                $this->response = new JsonResponse([
                    "error"             => "invalid_request",
                    "error_description" => $translator->translate("missing_client_id", "oauth")
                ], 400);
                return;
            }
            // Fail if client details have been passed in both via the header and query params.
            if (($clientId || $clientSecret) && $authHeaderDetails) {
                $this->response = new JsonResponse([
                    "error"             => "invalid_request",
                    "error_description" => $translator->translate("multiple_client_details", "oauth")
                ], 400);
                return;
            // If only the Authorization header was used, pass those details into the client ID and secret vars.
            } else if ($authHeaderDetails) {
                [$clientId, $clientSecret] = $authHeaderDetails;
            }
            
            // Fetch redirect URI.
            $redirectUri = $this->getParameter("redirect_uri", null);
            
            // Fetch table cache.
            /**
             * @var \Alder\Db\TableCache $tableCache
             */
            $tableCache = DiContainer::get()->get("alder_pa_table_cache");
            
            // Fetch client, fail if none match provided client ID.
            /**
             * @var \Alder\PublicAuthentication\Db\Row\Client $client
             */
            $client = $tableCache->fetchTable("Client")->getByPrimaryKey([(int) $clientId]);
            if (!$client) {
                $this->response = new JsonResponse([
                    "error" => "invalid_request",
                    "error_description" => $translator->translate("invalid_client_id", "oauth")
                ], 400);
                return;
            }
            
            // If the client is confidential, ensure that the request was sent with the client's
            // secret. If no secret was provided, or the secret provided was invalid, then fail.
            if ($client["secret"] !== null) {
                if ($clientSecret !== $client["secret"]) {
                    $this->response = new JsonResponse([
                        "error" => "invalid_request",
                        "error_description" => $translator->translate("invalid_client_secret", "oauth")
                    ], 401);
                    return;
                }
            } else if ($grantType === "client_credentials") {
                // NOTE: Public clients can change client credentials via the end-user assigned as owner.
                $this->response = new JsonResponse([
                    "error" => "unauthorized_client",
                    "error_description" => $translator->translate("only_confidential_clients", "oauth")
                ], 401);
                return;
            }
            
            // Fetch client registered redirect URIs, fail if none registered.
            $clientRedirects = $client->getRegisteredRedirects();
            if (!$clientRedirects || !in_array($redirectUri, $clientRedirects)) {
                $this->response = new JsonResponse([
                    "error" => "invalid_request",
                    "error_description" => $translator->translate("invalid_redirect_uri", "oauth")
                ], 400);
                return;
            }
            
            $responseData = [
                "token_type" => "bearer",
            ];
            // If grant type is authorization_code, validate the code and generate the access token.
            if ($grantType === "authorization_code") {
                $code = $this->getParameter("code", null);
                if (!$code) {
                    $this->response = new JsonResponse([
                        "error" => "invalid_grant",
                        "error_description" => $translator->translate("missing_auth_code", "oauth")
                    ], 400);
                    return;
                }
                
                // Parse the authorisation code.
                $codeAsToken = (new Parser())->parse($code);
                
                // If authorisation code is valid, generate access token.
                if (!TokenValidator::validate($codeAsToken, ["public" => ["sub" => "cat"], "private" => [ "cid" => $clientId, "ruri" => $redirectUri ]])) {
                    $this->response = new JsonResponse([
                        "error" => "invalid_grant",
                        "error_description" => $translator->translate("invalid_auth_code", "oauth")
                    ], 401);
                    return;
                }
                
                $scopes = $codeAsToken->getClaim("scp");
                $userId = $codeAsToken->getClaim("uid");
                
                $responseData["access_token"] = UserAccessTokenFactory::create($clientId, $userId, $scopes);
                $responseData["refresh_token"] = UserRefreshTokenFactory::create($responseData["access_token"]);
            } else if ($grantType === "password") {
                // Fetch allowed scope of authorisation request if grant type was password.
                $scopes = $client->getAllowedScopes($this->getParameter("scope", Client::FULL_SCOPE));
                if ($scopes === null) {
                    $this->response = new JsonResponse([
                        "error" => "server_error",
                        "error_description" => $translator->translate("server_failed")
                    ], 500);
                    throw new \OAuthException($translator->translate("scope_resolution_failed", "oauth"), E_USER_ERROR);
                } else if (empty($scopes)) {
                    $this->response = new JsonResponse([
                        "error" => "access_denied",
                        "error_description" => $translator->translate("no_accessible_scopes", "oauth")
                    ], 400);
                    return;
                }
                
                // Get passed in username (or email) and password.
                $usernameOrEmail = $this->getParameter("username", null);
                $password = $this->getParameter("password", null);
                
                // Get user (only ID and password hash) from provided username or email.
                /**
                 * @var \Alder\PublicAuthentication\Db\Table\User $userTable
                 */
                $userTable = $tableCache->fetchTable("User");
                $user = $userTable->getByUsernameOrEmail($usernameOrEmail);
                
                // If user was not found, fail.
                if (!$user) {
                    $this->response = new JsonResponse([
                        "error"             => "invalid_grant",
                        "error_description" => $translator->translate("invalid_user_credentials", "oauth")
                    ]. 400);
                    return;
                }
                
                // Verify password provided was correct.
                if (!$user->passwordValid($password)) {
                    $this->response = new JsonResponse([
                        "error" => "invalid_grant",
                        "error_description" => $translator->translate("invalid_user_credentials", "oauth")
                    ], 400);
                    return;
                }
                
                // Generate access token and refresh token.
                $responseData["access_token"] = UserAccessTokenFactory::create($clientId, $user["id"], $scopes);
                $responseData["refresh_token"] = UserRefreshTokenFactory::create($responseData["access_token"]);
            } else {
                $responseData["access_token"] = ClientAccessTokenFactory::create($clientId);
            }
            
            // If either token wasn't generated well, fail.
            if (!$responseData["access_token"]
                || (isset($responseData["refresh_token"]) && !$responseData["refresh_token"])) {
                $this->response = new JsonResponse([
                    "error" => "server_error",
                    "error_description" => $translator->translate("server_failed")
                ], 500);
                throw new \OAuthException($translator->translate("token_generation_failed", "oauth"), E_USER_ERROR);
            }
            
            // Complete response data and set response to include it in body as JSON encoded string.
            $responseData["expires_in"] =  $responseData["access_token"]->getClaim("exp") - $responseData["access_token"]->getClaim("iat");
            $this->response = new JsonResponse($responseData);
        }
    }
