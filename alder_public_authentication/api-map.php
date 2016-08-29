<?php

    $defaultOptionsDetails = [
            "description" => "Provides details of various operations possible on this URI.",
            "parameters" => [],
            "options" => []
    ];

    // TODO(Matthew): Constantly Update.
    // TODO(Matthew): Determine what else but parameters need to be specified (headers, error codes etc.).
    // TODO(Matthew): Should ACL consideration be more granular (/ dynamic)?
    /**
     * The API map for the public authentication module of Alder.
     *
     * Spec:
     *     <ACTION> => [
     *         "allow" => <METHODS>
     *         "body" => [
     *             <METHOD> => [
     *                 "description" => <DESCRIPTION>,
     *                 "parameters" => [
     *                     <PARAMETER> => [
     *                         "type" => <TYPE>
     *                         "description" => <DESCRIPTION>
     *                         "required" => bool [default: false]
     *                         "requiredIfNotSet" => <PARAMETER_LIST>
     *                         "mustBeAuthenticated" => bool [default: false]
     *                         "mustBeStaff" => bool [default: false]
     *                     ]
     *                 ],
     *                 "examples" => [
     *                     [
     *                         <PARAMETER> => <VALUE>,
     *                         <PARAMETER> => <VALUE>,
     *                         ...
     *                     ]
     *                 ]
     *             ]
     *         ]
     *     ]
     *
     *     <ACTION>          ->  "authentication"
     *     <METHODS>         ->  "GET,POST,OPTIONS"
     *     <METHOD>          ->  "GET"
     *     <DESCRIPTION>     ->  "Authenticates the provided user details."
     *     <PARAMETER>       ->  "password"
     *     <TYPE>            ->  "string"
     *     <PARAMETER_LIST>  ->  "password,email;username" // Reads as: if password and email are not set OR if username is not set.
     *     <VALUE>           ->  "john.smith42"
     */
    return [
        AUTH => [
            "allow" => "POST,OPTIONS",
            "body" => [
                "OPTIONS" => $defaultOptionsDetails,
                "POST" => [
                    "description" => "Authenticates the provided user details and returns a session token if valid.",
                    "parameters" => [
                        "password" => [
                            "type" => "string",
                            "description" => "The user's password.",
                            "required" => true
                        ],
                        "username" => [
                            "type" => "string",
                            "description" => "The user's username.",
                            "requiredIfNotSet" => "email"
                        ],
                        "email" => [
                            "type" => "string",
                            "description" => "The user's email.",
                            "requiredIfNotSet" => "username"
                        ],
                        "extended_session" => [
                            "type" => "boolean",
                            "description" => "Whether the session should last for an extended period or not.",
                            "required" => false
                        ]
                    ],
                    "examples" => [
                        [
                            "username" => "john.smith42",
                            "password" => "Password123",
                            "extended_session" => false
                        ],
                        [
                            "email" => "john.smith@example.com",
                            "password" => "SuperDuperSecurePass4321",
                            "extended_session" => true
                        ]
                    ]
                ]
            ]
        ],
        LICENSE => [
            "allow" => "GET,POST,PATCH,PUT,DELETE,OPTIONS,HEAD",
            "body" => [
                "OPTIONS" => $defaultOptionsDetails,
                "GET" => [
                    "description" => "Retrieves the specified license object and returns it if it exists.",
                    "parameters" => [

                    ],
                    "examples" => [

                    ]
                ]
            ]
        ],
        USER => [

        ],
        USER_LICENSE => [

        ]
    ];

