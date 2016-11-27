<?php
    
    namespace Alder\PublicAuthentication\Client;
    
    use Alder\DiContainer;
    use Alder\Db\Table\AbstractTable;
    use Alder\Token\Factory as TokenFactory;
    
    use Lcobucci\JWT\Token;
    
    /**
     * Factory for creating access tokens for clients.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class ClientAccessTokenFactory
    {
        /**
         * Creates an access token for the client with the given client ID with
         * the scope as provided. Stores the issuance of this token in the access token
         * database.
         *
         * Usage safety: client ID is assumed to be provided correctly and scopes requested
         * to be permissible for the given client.
         *
         * @param int $clientId The ID of the client to create the access token for.
         *
         * @return \Lcobucci\JWT\Token|null Either the code string on successful creation or null.
         */
        public static function create(int $clientId) : ?Token {
            /* Generate the token. */
            // Fetch application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Generate the access token.
            $token = TokenFactory::create(
                [
                    "sub" => "cat" // "Client Access Token"
                ],
                [
                    "cid" => $clientId
                ],
                $config["public_authentication"]["client_access_token"]["duration"]
            );
            
            /* Store token metadata in database. */
            // Fetch table cache.
            $tableCache = DiContainer::get()->get("alder_pa_table_cache");
            // Fetch client access token table.
            /**
             * @var AbstractTable $accessTokenTable
             */
            $accessTokenTable = $tableCache->fetchTable("ClientAccessToken");
            
            // Insert the access token metadata into the database.
            $accessTokenTable->insert(
                [
                    "token_id"  => $token->getClaim("jti"),
                    "client_id" => $clientId
                ]
            );
            
            // Return token.
            return $token;
        }
    }
