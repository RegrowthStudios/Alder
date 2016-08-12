<?php
    
    namespace Alder\PublicAuthentication\User;
    
    use Alder\Container;
    use Alder\Token\TokenFactory;
    
    /**
     * Factory for creating user sessions.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class SessionFactory
    {
        public static function create($id, array $data = [], $extendedSession = false) {
            if (count($data) != 4 ||
                    !(isset($data["username"]) && isset($data["primary_email"])
                    && isset($data["license_keys"]) && isset($data["staff_member"]))) {
                // Access database model of user with given ID.
                // Fail if ID is invalid, otherwise merge with data that does exist.
            }
            
            $config = Container::get()->get("config")["alder"];
            if (!$extendedSession) {
                $sessionLength = $config["public_authentication"]["session"]["duration"];
            } else {
                $sessionLength = $config["public_authentication"]["session"]["duration_extended"];
            }
            
            return TokenFactory::create([
                "tokenLifetime" => $sessionLength,
                "registeredClaims" => [
                    "sub" => "user"
                ],
                "applicationPayload" => [
                    "id" => $id,
                    "username" => $data["username"],
                    "primary_email" => $data["primary_email"],
                    "license_keys" => $data["license_keys"],
                    "staff_member" => $data["staff_member"]
                ]
            ]);
        }
    }
