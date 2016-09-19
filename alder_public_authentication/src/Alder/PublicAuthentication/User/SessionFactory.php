<?php
    
    namespace Alder\PublicAuthentication\User;
    
    use Alder\Container;
    use Alder\Error\Stack as ErrorStack;
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
        /**
         * Construct a session token for the
         *
         * @param int $id The ID of the user to create the token for.
         * @param \Alder\Error\Stack $errors The error stack to push errors onto.
         * @param array $data The data of the user to create the token for.
         * @param bool $extendedSession Whether the token should be for an extended session.
         *
         * @return \Alder\Token\Token|bool The created token, or false if the token could not be created.
         */
        public static function create($id, ErrorStack& $errors, array $data = [], $extendedSession = false) {
            $container = Container::get();

            if (! (isset($data["username"]) && isset($data["primary_email_local"])
                && isset($data["primary_email_domain"]) && isset($data["license_keys"])
                && isset($data["employee_flag"]))) {
                $user = $container->get("AlderTableCache")
                            ->fetchTable("User")->getById($id);
                if (!$user) {
                    return false;
                }
                $data = array_merge($user->toArray(), $data);
            }
            
            $config = $container->get("config")["alder"];
            
            if (!$extendedSession) {
                $sessionLength = $config["public_authentication"]["session"]["duration"];
            } else {
                $sessionLength = $config["public_authentication"]["session"]["duration_extended"];
            }
            
            $token = TokenFactory::create([
                "tokenLifetime" => $sessionLength,
                "registeredClaims" => [
                    "sub" => "user"
                ],
                "applicationPayload" => [
                    "id" => $id,
                    "username" => $data["username"],
                    "primary_email" => $data["primary_email_local"] . "@" . $data["primary_email_domain"],
                    "license_keys" => $data["license_keys"],
                    "employee_flag" => $data["employee_flag"],
                    "extended_session" => $extendedSession
                ]
            ]);
            
            // If token failed to be generated, fail.
            if (!$token) {
                $errors->push(103020101);
                return false;
            }
            
            $cookie = USER_SESSION . "=$sessionToken; Expires=" . gmstrftime("%a, %d %b %Y %H:%M:%S GMT", $sessionToken->getClaim("exp")) . "; Domain=" . $config["domain"] . "; Path=/";
            if ($config["security"]["cookies_over_https_only"] !== false) {
                $cookie .= "; Secure";
            }
            if ($config["security"]["acces_cookies_via_http_only"] !== false) {
                $cookie .= "; HttpOnly";
            }
            
            return $token;
        }
    }
