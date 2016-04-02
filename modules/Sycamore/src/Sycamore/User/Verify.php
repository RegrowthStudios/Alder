<?php
    namespace Sycamore\User;

    use Sycamore\Stdlib\ArrayUtils;
    use Sycamore\Token\JwtFactory;
    use Sycamore\Token\Jwt;

    use Zend\ServiceManager\ServiceLocatorInterface;

    /**
     * Verify has functions related to creation and checking of verification tokens.
     *
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @since 0.1.0
     */
    class Verify
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
         * Constructs a verification token.
         *
         * @param int $userId The ID of the user whom this token applies to.
         * @param array|\Traversable $items The items pertaining to this verification. E.g. a delete key.
         * @param int $tokenLifetime The lifetime of the constructed verification token.
         * @param string $purpose The purpose of this verification token.
         *
         * @return \Sycamore\Token\Jwt A JWT object containing the token string.
         */
        public function create($userId, $items, $tokenLifetime = 86400 /* 24 Hours */, $purpose = "verification")
        {
            $vaildatedItems = ArrayUtils::validateArrayLike($items, get_class($this), true);

            return JwtFactory::create($this->serviceManager, [
                "tokenLifetime" => $tokenLifetime,
                "registeredClaims" => [
                    "sub" => (string) $purpose
                ],
                "applicationPayload" => array_merge($vaildatedItems, [
                    "id" => $userId
                ])
            ]);
        }

        /**
         * Verifies the verification token.
         *
         * @param int $userId The ID of the user whom this token applies to.
         * @param string $tokenStr The token to verify.
         * @param array $itemsExpected The expected items if token is to be verfied. E.g. delete key.
         * @param string $purpose The purpose of this verification token.
         *
         * @return bool|array The application payload if the token is valid, otherwise false.
         */
        public function verify($userId, $tokenStr, $itemsExpected, $purpose = "verification")
        {
            $token = new Jwt($this->serviceManager, $tokenStr);
            // Ensure token is generally valid as per public claims.
            if (!$token->validate([
                "sub" => $purpose
            ])) {
                return false;
            }

            // Grab the domain of application.
            $domain = $this->serviceManager->get("Config")["Sycamore"]["domain"];

            // Get item claims.
            $payload = $token->getClaims();
            if (isset($payload[$domain])) {
                $itemClaims = $payload[$domain]->getValue();

                // If application payload and expected items + user ID are equivalent, then token is truly verified.
                if (empty( ArrayUtils::xorArrays( array_merge((array) $itemsExpected, [
                                        "id" => $userId
                                    ]), (array) $itemClaims))) {
                    return $itemClaims;
                }
            }

            return false;
        }
    }
