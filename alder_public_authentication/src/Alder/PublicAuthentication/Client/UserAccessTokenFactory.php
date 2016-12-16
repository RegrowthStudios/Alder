<?php
    
    namespace Alder\PublicAuthentication\Client;
    
    use Alder\DiContainer;
    use Alder\Db\Table\AbstractTable;
    use Alder\Error\Stack as ErrorStack;
    use Alder\Token\Factory as TokenFactory;
    
    use Lcobucci\JWT\Token;
    
    /**
     * Factory for creating access tokens for clients on behalf of end-users.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class UserAccessTokenFactory
    {
        /**
         * Creates an access token for the client with the given client ID with
         * the scope as provided. Stores the issuance of this token in the access token
         * database.
         *
         * Returns null if the user ID provided is null and the requestee has no established
         * user session.
         *
         * Usage safety: client ID is assumed to be provided correctly and scopes requested
         * to be permissible for the given client.
         *
         * @param int        $clientId The ID of the client to create the access token for.
         * @param int        $userId   The ID of the user to create the access token on behalf of. Set this to null for
         *                             implicit grants.
         * @param array      $scope    An array of strings specifying the scopes the code should authorise the
         *                             client to access.
         *
         * @return \Lcobucci\JWT\Token|null Either the code string on successful creation or null.
         */
        public static function create(int $clientId, ?int $userId, array $scope) : ?Token {
            // If provided user ID is null, implicit grant type there for get user ID from visitor.
            if ($userId === null) {
                // Grab visitor object.
                $visitor = DiContainer::get()->get("visitor");
                
                // Check if visitor is logged in. If not, no end-user to create auth code on behalf of.
                if (!$visitor["isLoggedIn"]) {
                    return null;
                }
                
                $userId = $visitor["id"];
            }
            
            /* Generate the token. */
            // Fetch application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Generate the access token.
            $token = TokenFactory::create(
                [
                    "sub" => "uat" // "User Access Token"
                ],
                [
                    "uid" => $userId,
                    "cid" => $clientId,
                    "scp" => join(" ", $scope)
                ],
                $config["public_authentication"]["user_access_token"]["duration"]
            );
            
            /* Store token metadata in database. */
            // Fetch table cache.
            $tableCache = DiContainer::get()->get("alder_pa_table_cache");
            // Fetch user access token table.
            /**
             * @var AbstractTable $accessTokenTable
             */
            $accessTokenTable = $tableCache->fetchTable("UserAccessToken");
            
            // Insert the access token metadata into the database.
            $accessTokenTable->insert(
                [
                    "token_id"  => $token->getClaim("jti"),
                    "client_id" => $clientId,
                    "user_id"   => $userId,
                    "scope"     => join(" ", $scope)
                ]
            );
            
            // Return token.
            return $token;
        }
    }
