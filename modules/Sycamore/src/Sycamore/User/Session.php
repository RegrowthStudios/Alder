<?php
    namespace Sycamore\User;

    use Sycamore\Token\Jwt;
    use Sycamore\Token\JwtFactory;

    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Session has functions related to creation and checking of user sessions.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Session
    {
        /**
         * The service manager for this application instance.
         *
         * @var \Zend\ServiceManager\ServiceLocatorInterface
         */
        protected $serviceManager;

        /**
         * Prepares the sercurity utility by injecting the service manager.
         *
         * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager The service manager for this instance of the application.
         */
        public function __construct(ServiceLocatorInterface& $serviceManager)
        {
            $this->serviceManager = $serviceManager;
        }

        /**
         * Creates a new user session.
         *
         * @param string $usernameOrEmail The username or email of the user to create a session token for.
         * @param bool $extendedSession Whether to make the session of an extended length or not.
         *
         * @return boolean True if cookie was successfully set, false otherwise.
         */
        public function create($usernameOrEmail, $extendedSession = false)
        {
            $tableCache = $this->serviceManager->get("SycamoreTableCache");
            $userTable = $tableCache->fetchTable("User");
            if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
                $user = $userTable->getByEmail($usernameOrEmail);
            } else {
                $user = $userTable->getByUsername($usernameOrEmail);
            }

            if (!$user) {
                return false;
            }

            $config = $this->serviceManager->get("Config")["Sycamore"];
            if (!$extendedSession) {
                $sessionLength = $config["security"]["sessionLength"];
            } else {
                $sessionLength = $config["security"]["sessionLengthExtended"];
            }

            $token = strval(JwtFactory::create([
                "tokenLifetime" => $sessionLength,
                "registeredClaims" => [
                    "sub" => "user"
                ],
                "applicationPayload" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "email" => $user->email,
                    "superuser" => $user->superUser
                ]
            ]));

            return setcookie("SLIS", "$token", time() + $sessionLength, "/", $config["domain"], $config["security"]["sessionsOverHttpsOnly"], $config["security"]["accessCookiesViaHttpOnly"]); // SLIS -> Sycamore Logged In Session
        }

        /**
         * Acquires a user session, if it exists.
         *
         * @param mixed $tokenClaimsDump A variable into which the acquired token's claims will be dumped.
         *
         * @return int The result of validating the token.
         */
        public function acquire(& $tokenClaimsDump)
        {
            $slis = filter_input(INPUT_COOKIE, "SLIS");
            if (!$slis) {
                return 0;
            }

            // Create JWT object.
            $token = new Jwt($this->serviceManager, $slis);

            // Dump claims.
            $tokenClaimsDump = $token->getClaims()[$this->serviceManager->get("Config")["Sycamore"]["domain"]];

            // Return validation of the JWT.
            return $token->validate([
                "validators" => [
                    "sub" => "user"
                ]
            ]);
        }
    }
