<?php
    
    namespace Alder\PublicAuthentication\Client;
    
    use Alder\DiContainer;
    use Alder\Db\Table\AbstractTable;
    use Alder\Token\Factory as TokenFactory;
    
    use Lcobucci\JWT\Token;
    
    /**
     * Factory for creating refresh tokens for clients on behalf of end-users.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class UserRefreshTokenFactory
    {
        /**
         * Creates a refresh token for the access token provided. Stores the issuance of
         * this token in the refresh token database.
         *
         * @param \Lcobucci\JWT\Token $accessToken The access token to create the refresh token for.
         *
         * @return \Lcobucci\JWT\Token The generated refresh token.
         */
        public static function create(Token $accessToken) : Token {
            /* Generate the token. */
            // Fetch application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Generate refresh token.
            $token = TokenFactory::create(
                [
                    "sub" => "urt" // "User Refresh Token"
                ],
                [
                    "uid" => $accessToken->getClaim("uid"),
                    "cid" => $accessToken->getClaim("cid"),
                    "scp" => $accessToken->getClaim("scp")
                ],
                $config["public_authentication"]["user_refresh_token"]["duration"]
            );
    
            /* Store token metadata in database. */
            // Fetch table cache.
            $tableCache = DiContainer::get()->get("alder_pa_table_cache");
            // Fetch client access token table.
            /**
             * @var AbstractTable $refreshTokenTable
             */
            $refreshTokenTable = $tableCache->fetchTable("UserRefreshToken");
    
            // Insert the access token metadata into the database.
            $refreshTokenTable->insert(
                [
                    "token_id"  => $token->getClaim("jti"),
                    "client_id" => $accessToken->getClaim("cid"),
                    "user_id"   => $accessToken->getClaim("uid"),
                    "scope"     => $accessToken->getClaim("scp")
                ]
            );
            
            // Return the token.
            return $token;
        }
    }
