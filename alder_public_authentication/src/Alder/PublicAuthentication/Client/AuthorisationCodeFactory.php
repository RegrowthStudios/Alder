<?php
    
    namespace Alder\PublicAuthentication\Client;
    
    use Alder\DiContainer;
    use Alder\Error\Stack as ErrorStack;
    use Alder\Token\Factory as TokenFactory;
    
    use Lcobucci\JWT\Token;
    
    // TODO(Matthew): Consider renaming to be more clear. Factory implies just creation.
    /**
     * Factory for creating authorisation codes for clients.
     *
     * @author    Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since     0.1.0
     */
    class AuthorisationCodeFactory
    {
        /**
         * Creates an authorisation code for the client with the given client ID with
         * the scope as provided.
         *
         * Usage safety: client ID is assumed to be provided correctly and scopes requested
         * to be permissible for the given client.
         *
         * @param int        $clientId    The ID of the client to create the auth code for.
         * @param string     $redirectUri The URI to redirect to after validating auth code.
         * @param array      $scope       An array of strings specifying the scopes the code should authorise the
         *                                client to access.
         *
         * @return \Lcobucci\JWT\Token|null Either the code string on successful creation or null.
         */
        public static function create(int $clientId, string $redirectUri, array $scope) : ?Token {
            // Grab visitor object.
            $visitor = DiContainer::get()->get("visitor");
            
            // Check if visitor is logged in. If not, no end-user to create auth code on behalf of.
            if (!$visitor["isLoggedIn"]) {
                return null;
            }
            
            // Fetch application config.
            $config = DiContainer::get()->get("config")["alder"];
            
            // Generate the authorisation code.
            $token = TokenFactory::create(
                [
                    "sub" => "cac" // "Client Authorisation Code"
                ],
                [
                    "uid"  => $visitor["id"],
                    "cid"  => $clientId,
                    "scp"  => join(" ", $scope),
                    "ruri" => $redirectUri
                ],
                $config["public_authentication"]["client_auth_code"]["duration"]
            );
            
            return $token;
        }
    }
