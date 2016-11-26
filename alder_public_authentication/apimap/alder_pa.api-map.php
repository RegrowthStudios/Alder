<?php

    $defaultOptionsDetails = [
        "description" => "Provides details of various operations possible on this URI.",
        "parameters" => [
            "show_all" => [
                "type" => "boolean",
                "description" => "Whether to show the API map without discounting methods disallowed at current authentication level of the requestor.",
                "default" => "true"
            ]
        ],
        "response" => [
            "parameters" => [
                "items" => [
                    "type" => "array",
                    "description" => "The API map for the given URI or entire API as appropriate."
                ],
                "metadata" => [
                    "show_all" => [
                        "type" => "boolean",
                        "description" => "Whether the response contains the API map without discounting methods disallowed at current authentication level of the requestor.",
                        "matches_parameter" => "show_all"
                    ]
                ]
            ]
        ],
        "valid_request_example" => [
            "parameters" => [
                "show_all" => false
            ],
            "requests" => [
                [
                    "uri" => "https://www.example.com/genericAction?show_all=0"
                ]
            ]
        ]
    ];
    
    // TODO(Matthew): Constantly Update.
    // TODO(Matthew): Determine what else but parameters need to be specified (headers, error codes etc.).
    // TODO(Matthew): Consider all header fields defined in HTTP/1.1 (RFC2616-sec14)
    /**
     * Static components of the API map.
     *
     * Spec:
     *     <ACTION> => [
     *         "allow" => <METHODS>
     *         "body" => [
     *             <METHOD> => [
     *                 "description" => <DESCRIPTION>,
     *                 "multiple_parameter_sets_allowed" => bool [default: true],
     *                 "parameters" => [
     *                     <PARAMETER> => [
     *                         "type" => <TYPE>,
     *                         "description" => <DESCRIPTION>,
     *                         "required" => bool [default: false],
     *                         "default" => <VALUE>
     *                     ],
     *                     ...,
     *                     <NAMESPACE> => [
     *                         <PARAMETER> => [ ... ],
     *                         ...
     *                     ],
     *                     ...,
     *                     [ // Implies multiple sets of the parameters inside may be sent and processed simultaneously.
     *                         <PARAMETER> => [ ... ],
     *                         ...
     *                     ],
     *                     ...
     *                 ],
     *                 "response" => [
     *                     "items" => [
     *                         "type" => <TYPE>,
     *                         "description" => <DESCRIPTION>
     *                     ],
     *                     "metadata" => [
     *                         <PARAMETER> => [
     *                             "type" => <TYPE>,
     *                             "description" => <DESCRIPTION>,
     *                             "matches_parameter" => <PARAMETER> // If set, the value of this metadata parameter will be the same as the specified request parameter.
     *                         ],
     *                     ]
     *                 ]
     *                 "examples" => [
     *                     [
     *                         "parameters" => [
     *                             <PARAMETER> => <VALUE>,
     *                             <NAMESPACE> => [
     *                                 <PARAMETER> => <VALUE>,
     *                                 ...
     *                             ]
     *                             ...
     *                             ]
     *                         ],
     *                         "uri" => <URI>,
     *                         "alt_uris" => [ // Only applicable if the URIs contain exactly the same information.
     *                             <URI>
     *                             ...
     *                         ],
     *                         "body" => <BODY>,
     *                         "content_type" => <CONTENT_TYPE>
     *                     ],
     *                     ...
     *                 ]
     *             ]
     *         ]
     *     ]
     *
     * Legend:
     *     <ACTION>          ->  "authentication"
     *     <METHODS>         ->  "GET,POST,OPTIONS"
     *     <METHOD>          ->  "GET"
     *     <DESCRIPTION>     ->  "Authenticates the provided user details."
     *     <PARAMETER>       ->  "password"
     *     <NAMESPACE>       ->  "data"
     *     <TYPE>            ->  "string"
     *     <VALUE>           ->  "john.smith42"
     *     <URI>             ->  "https://www.example.com/"
     *     <BODY>            ->  "data={"data":{"username":"john.smith42","password":"Password123","extended_session":false}}"
     *     <CONTENT_TYPE>    ->  "application/x-www-form-urlencoded"
     */
    return [
        "public_authentication" => [
            // TODO(Matthew): Consider two-factor authentication.
            AUTHENTICATE => [
                "allow" => "POST,OPTIONS",
                "relative_uri" => "/auth",
                "body" => [
                    "OPTIONS" => $defaultOptionsDetails,
                    "POST" => [
                        "description" => "Authenticates the provided user details and returns a session token if valid.",
                        "parameters" => [
                            "data" => [
                                [
                                    "password" => [
                                        "type" => "string",
                                        "description" => "The user's password.",
                                        "required" => true
                                    ],
                                    "username" => [
                                        "type" => "string",
                                        "description" => "The user's username.",
                                        "required_if_not_set" => "email"
                                    ],
                                    "email" => [
                                        "type" => "string",
                                        "description" => "The user's email.",
                                        "required_if_not_set" => "username"
                                    ],
                                    "extended_session" => [
                                        "type" => "boolean",
                                        "description" => "Whether the session should last for an extended period or not."
                                    ]
                                ]
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "data" => [
                                    [
                                        "username" => "john.smith42",
                                        "password" => "Password123",
                                        "extended_session" => false
                                    ]
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/auth?data=[{\"username\":\"john.smith42\",\"password\":\"Password123\",\"extended_session\":false}]"
                                ],
                                [
                                    "uri" => "https://www.example.com/auth",
                                    "content_type" => "application/x-www-form-urlencoded",
                                    "body" => "data=[{\"username\":\"john.smith42\",\"password\":\"Password123\",\"extended_session\":false}]"
                                ],
                                [
                                    "uri" => "https://www.example.com/auth",
                                    "content_type" => "application/json",
                                    "body" => "{\"data\":[{\"username\":\"john.smith42\",\"password\":\"Password123\",\"extended_session\":false}]}"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            LICENSE => [
                "allow" => "GET,POST,PATCH,PUT,DELETE,OPTIONS,HEAD",
                "properties" => [
                    "id" => [
                        "type" => "integer",
                        "min_value" => 1,
                        "max_value" => MYSQL_INT_MAX,
                        "unique" => true,
                        "description" => "The ID of the license."
                    ],
                    "etag" => [
                        "type" => "string",
                        "unique" => true,
                        "description" => "The ETag uniquely identifying this version of the license."
                    ],
                    "last_change_timestamp" => [
                        "type" => "integer",
                        "min_value" => 0,
                        "max_value" => MYSQL_INT_MAX,
                        "unique" => false,
                        "description" => "The time that the license was last changed.",
                    ],
                    "creation_timestamp" => [
                        "type" => "integer",
                        "min_value" => 0,
                        "max_value" => MYSQL_INT_MAX,
                        "unique" => false,
                        "description" => "The time that the license was created.",
                    ],
                    "name" => [
                        "type" => "string",
                        "min_length" => 1,
                        "max_length" => 50,
                        "unique" => false,
                        "description" => "The name of the license."
                    ],
                    "description" => [
                        "type" => "string",
                        "min_length" => 1,
                        "max_length" => 512,
                        "unique" => false,
                        "description" => "The description of the license."
                    ],
                    "product_id" => [
                        "type" => "integer",
                        "min_value" => 1,
                        "max_value" => MYSQL_INT_MAX,
                        "unique" => false,
                        "description" => "The ID of the associated product."
                    ],
                    "simultaneous_usage_count" => [
                        "type" => "integer",
                        "min_value" => 1,
                        "max_value" => 255,
                        "unique" => false,
                        "description" => "The number of simultaneous usages that may be made of the license."
                    ]
                ],
                "body" => [
                    "OPTIONS" => $defaultOptionsDetails,
                    "GET" => [
                        "description" => "Retrieves the specified license object and returns it if it exists.",
                        "parameters" => [
                            "id" => [
                                "type" => "integer|string",
                                "description" => "The license's ID. Value must be an integer or numeric string.",
                                "default" => NULL,
                                "required" => true
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "id" => 3
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license/3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "id=3",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"id\":3}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "GET.bulk" => [ // TODO(Matthew): Consider if If-Range is needed to be local, if so ensure the header If-Range is still satisfiable.
                        "description" => "Retrieves and returns the set of license objects that satisfy the provided filters.",
                        "parameters" => [
                            "general" => [
                                "results_per_page" => [
                                    "type" => "integer",
                                    "description" => "Specifies the number of items to include per page.",
                                    "min" => 1,
                                    "max" => 50,
                                    "default" => 5
                                ],
                                "page_token" => [
                                    "type" => "string",
                                    "description" => "The value of a chosen property of the last sent item that can be used to fetch the next page of items with values equal to or beyond. This token should be either the \"next_token\" or \"prev_token\" provided by a previous request with the same parameters.",
                                    "default" => NULL
                                ],
                                "order_by" => [
                                    "type" => "string",
                                    "allowed_values" => "id,product_id,creation_timestamp,last_change_timestamp",
                                    "default" => "id"
                                ]
                            ],
                            "specific" => [
                                [
                                    "filters" => [
                                        "id" => [
                                            "type" => "integer|string",
                                            "description" => "The license's ID. Value must be an integer or numeric string if encoding a single ID. Multiple IDs may be encoded into a string by comma separating each ID.",
                                            "default" => NULL
                                        ],
                                        "id_min" => [
                                            "type" => "integer|string",
                                            "description" => "The minimum value for license ID. Value must be an integer or numeric string.",
                                            "default" => NULL
                                        ],
                                        "id_max" => [
                                            "type" => "integer|string",
                                            "description" => "The license's ID. Value must be an integer or numeric string.",
                                            "default" => NULL
                                        ],
                                        "etag" => [
                                            "type" => "string[]",
                                            "description" => "An array of ETags that the entities found with the specified filters must match. Has analogous meaning as the If-None-Match header specified in 3.1 of RFC 7232. If no entities match an etag in this filter, then no entities will be returned from this filter set. If an ETag is present in both this and the \"not_etag\" filter, then a 400 status code will be yielded.",
                                            "default" => NULL
                                        ],
                                        "not_etag" => [
                                            "type" => "string[]",
                                            "description" => "An array of ETags that the entities found with the specified filters must NOT match. Has analogous meaning as the If-None-Match header specified in 3.2 of RFC 7232. If any entity matches an etag in this filter, then that entity will not be returned from this filter set. If an ETag is present in both this and the \"etag\" filter, then a 400 status code will be yielded.",
                                            "default" => NULL
                                        ],
                                        "last_change_before" => [
                                            "type" => "integer|string",
                                            "description" => "The time before which matching entities must have last been modified.",
                                            "default" => NULL
                                        ],
                                        "last_change_after" => [
                                            "type" => "integer|string",
                                            "description" => "The time after which matching entities must have last been modified.",
                                            "default" => NULL
                                        ],
                                        "creation_before" => [
                                            "type" => "integer|string",
                                            "description" => "The time before which matching entities must have been created.",
                                            "default" => NULL
                                        ],
                                        "creation_after" => [
                                            "type" => "integer|string",
                                            "description" => "The time after which matching entities must have been created.",
                                            "default" => NULL
                                        ]
                                    ],
                                    "preconditions" => [
                                        "if_range" => [
                                            "type" => "integer|string|string[]",
                                            "description" => "A list of ETags or an epoch timestamp. If a list of ETags and the ETags of the entities found matches it, or else an epoch timestamp and the entities found have not been modified since it then the Range header provided will be satisfied, if no Range header is provided then this parameter will be ignored. Value must either be a string if an ETag is provided or an integer or numeric string if an epoch timestamp is being provided. Has analogous meaning to the If-Range header specified in 3.2 of RFC 7233.",
                                            "default" => NULL
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "general" => [
                                    "results_per_page" => 10,
                                ],
                                "specific" => [
                                    [
                                        "filters" => [
                                            "id" => "3,4,5"
                                        ],
                                        "preconditions" => [
                                            "if_range" => 1473720896
                                        ]
                                    ],
                                    [
                                        "filters" => [
                                            "creation_before" => 1323749586,
                                            "not_etag" => [
                                                "aeofpd3ls",
                                                "apf84rkdp"
                                            ]
                                        ],
                                        "preconditions" => [
                                            "if_range" => 1473720896
                                        ]
                                    ]
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license?general={\"results_per_page\":10}&data=[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "general={\"results_per_page\":10}&data=[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"general\":{\"results_per_page\":10},\"data\":[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "POST" => [
                        "description" => "Creates an entity for each set of data provided.",
                        "parameters" => [
                            "data" => [
                                [
                                    "name" => [
                                        "type" => "string",
                                        "max_length" => 50,
                                        "description" => "The name of the license.",
                                        "required" => true
                                    ],
                                    "description" => [
                                        "type" => "string",
                                        "max_length" => 512,
                                        "description" => "The description of the license.",
                                        "required" => false
                                    ],
                                    // TODO(Matthew): Move this out to its own table for translation purposes.
                                    "legal_text" => [
                                        "type" => "string",
                                        "max_length" => 16777215,
                                        "description" => "The legal text of the license.",
                                        "required" => true
                                    ],
                                    // TODO(Matthew): Move this out to its own table for translation purposes.
                                    "plain_text" => [
                                        "type" => "string",
                                        "max_length" => 16777215,
                                        "description" => "The plain version of the legal text of the license.",
                                        "required" => true
                                    ],
                                    "product_id" => [
                                        "type" => "integer",
                                        "foreign_key" => "store:product:id", // TODO(Matthew): Provide look-up table in map with "store" => [ "url" => "store.example.com" ] for allowing the discovery of remote foreign keys.
                                        //                See http://stackoverflow.com/questions/4452132/add-foreign-key-relationship-between-two-databases
                                        "description" => "The associated product's ID.",
                                        "required" => true
                                    ],
                                    "simultaneous_usage_count" => [
                                        "type" => "integer",
                                        "min_length" => -1,
                                        "max_length" => 128,
                                        "description" => "The number of usages that may be made of the license simultaneously.",
                                        "required" => true
                                    ]
                                ]
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "data" => [
                                    [
                                        "name" => "Standard License for A",
                                        "description" => "The standard license for product A.",
                                        "legal_text" => "Your soul belongs to the devil.",
                                        "plain_text" => "You probably shouldn't agree to this deal.",
                                        "product_id" => 1,
                                        "simultaneous_usage_count" => 1
                                    ],
                                    [
                                        "name" => "Student License for B",
                                        "description" => "The student license for product B.",
                                        "legal_text" => "Here is some legalese.",
                                        "plain_text" => "Here's an ELI5 version.",
                                        "product_id" => 2,
                                        "simultaneous_usage_count" => 1
                                    ]
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license?data=[{\"name\":\"Standard License for A\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1},{\"name\":\"Student License for B\",\"description\":\"The student license for product B.\",\"legal_text\":\"Here is some legalese.\",\"plain_text\":\"Here's an ELI5 version.\",\"product_id\":2,\"simultaneous_usage_count\":1}]"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "data=[{\"name\":\"Standard License for A\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1},{\"name\":\"Student License for B\",\"description\":\"The student license for product B.\",\"legal_text\":\"Here is some legalese.\",\"plain_text\":\"Here's an ELI5 version.\",\"product_id\":2,\"simultaneous_usage_count\":1}]",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{data:[{\"name\":\"Standard License for A\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1},{\"name\":\"Student License for B\",\"description\":\"The student license for product B.\",\"legal_text\":\"Here is some legalese.\",\"plain_text\":\"Here's an ELI5 version.\",\"product_id\":2,\"simultaneous_usage_count\":1}]}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "PATCH" => [ // TODO(Matthew): Add Accept-Patch: application/json header as per RFC 5789.
                         "description" => "Patches the specified license object.",
                         "parameters" => [
                             "id" => [
                                 "type" => "integer|string",
                                 "description" => "The license's ID. Value must be an integer or numeric string.",
                                 "default" => NULL,
                                 "required" => true
                             ],
                             "data" => [
                                 "name" => [
                                     "type" => "string",
                                     "max_length" => 50,
                                     "description" => "The name of the license."
                                 ],
                                 "description" => [
                                     "type" => "string",
                                     "max_length" => 512,
                                     "description" => "The description of the license."
                                 ],
                                 // TODO(Matthew): Move this out to its own table for translation purposes.
                                 "legal_text" => [
                                     "type" => "string",
                                     "max_length" => 16777215,
                                     "description" => "The legal text of the license."
                                 ],
                                 // TODO(Matthew): Move this out to its own table for translation purposes.
                                 "plain_text" => [
                                     "type" => "string",
                                     "max_length" => 16777215,
                                     "description" => "The plain version of the legal text of the license."
                                 ],
                                 "product_id" => [
                                     "type" => "integer",
                                     "foreign_key" => "store:product:id", // TODO(Matthew): Provide look-up table in map with "store" => [ "url" => "store.example.com" ] for allowing the discovery of remote foreign keys.
                                     //                See http://stackoverflow.com/questions/4452132/add-foreign-key-relationship-between-two-databases
                                     "description" => "The associated product's ID."
                                 ],
                                 "simultaneous_usage_count" => [
                                     "type" => "integer",
                                     "min_length" => -1,
                                     "max_length" => 128,
                                     "description" => "The number of usages that may be made of the license simultaneously."
                                 ]
                             ],
                             "preconditions" => [
                                 "if_match",
                                 "if_not_match",
                                 "if_last_modified_since",
                                 "if_last_modified_before",
                                 "if_range"
                             ]
                         ],
                         "valid_request_example" => [
                             "parameters" => [
                                 "id" => 3,
                                 "data" => [
                                     "name" => "New License Name"
                                 ]
                             ],
                             "requests" => [
                                 [
                                     "uri" => "https://www.example.com/license/3?data={\"data\":{\"name\":\"New License Name\"}}"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license/3",
                                     "body" => "data={\"name\":\"New License Name\"}",
                                     "content_type" => "application/x-www-form-urlencoded"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license/3",
                                     "body" => "{\"data\":{\"name\":\"New License Name\"}}",
                                     "content_type" => "application/json"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license?id=3&data={\"data\":{\"name\":\"New License Name\"}}"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license?id=3",
                                     "body" => "data={\"name\":\"New License Name\"}",
                                     "content_type" => "application/x-www-form-urlencoded"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license?id=3",
                                     "body" => "{\"data\":{\"name\":\"New License Name\"}}",
                                     "content_type" => "application/json"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license",
                                     "body" => "id=3&data={\"name\":\"New License Name\"}",
                                     "content_type" => "application/x-www-form-urlencoded"
                                 ],
                                 [
                                     "uri" => "https://www.example.com/license",
                                     "body" => "{\"id\":3,\"data\":{\"name\":\"New License Name\"}}",
                                     "content_type" => "application/json"
                                 ]
                             ]
                         ]
                    ],
                    "PATCH.bulk" => [
            
                    ],
                    "PUT" => [
                        "description" => "Patches the specified license object.",
                        "parameters" => [
                            "id" => [
                                "type" => "integer|string",
                                "description" => "The license's ID. Value must be an integer or numeric string.",
                                "default" => NULL,
                                "required" => true
                            ],
                            "data" => [
                                "name" => [
                                    "type" => "string",
                                    "max_length" => 50,
                                    "description" => "The name of the license.",
                                    "required" => true
                                ],
                                "description" => [
                                    "type" => "string",
                                    "max_length" => 512,
                                    "description" => "The description of the license.",
                                    "required" => false
                                ],
                                // TODO(Matthew): Move this out to its own table for translation purposes.
                                "legal_text" => [
                                    "type" => "string",
                                    "max_length" => 16777215,
                                    "description" => "The legal text of the license.",
                                    "required" => true
                                ],
                                // TODO(Matthew): Move this out to its own table for translation purposes.
                                "plain_text" => [
                                    "type" => "string",
                                    "max_length" => 16777215,
                                    "description" => "The plain version of the legal text of the license.",
                                    "required" => true
                                ],
                                "product_id" => [
                                    "type" => "integer",
                                    "foreign_key" => "store:product:id", // TODO(Matthew): Provide look-up table in map with "store" => [ "url" => "store.example.com" ] for allowing the discovery of remote foreign keys.
                                    //                See http://stackoverflow.com/questions/4452132/add-foreign-key-relationship-between-two-databases
                                    "description" => "The associated product's ID.",
                                    "required" => true
                                ],
                                "simultaneous_usage_count" => [
                                    "type" => "integer",
                                    "min_length" => -1,
                                    "max_length" => 128,
                                    "description" => "The number of usages that may be made of the license simultaneously.",
                                    "required" => true
                                ]
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "id" => 3,
                                "data" => [
                                    "name" => "Replacement License Name",
                                    "description" => "The standard license for product A.",
                                    "legal_text" => "Your soul belongs to the devil.",
                                    "plain_text" => "You probably shouldn't agree to this deal.",
                                    "product_id" => 1,
                                    "simultaneous_usage_count" => 1
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license/3?data={\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}"
                                ],
                                [
                                    "uri" => "https://www.example.com/license/3",
                                    "body" => "data={\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license/3",
                                    "body" => "{data:{\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}}",
                                    "content_type" => "application/json"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3&data={\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3",
                                    "body" => "data={\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3",
                                    "body" => "{\"data\":{\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}}",
                                    "content_type" => "application/json"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "id=3&data={\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"id\":3,\"data\":{\"name\":\"Replacement License Name\",\"description\":\"The standard license for product A.\",\"legal_text\":\"Your soul belongs to the devil.\",\"plain_text\":\"You probably shouldn't agree to this deal.\",\"product_id\":1,\"simultaneous_usage_count\":1}}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "PUT.bulk" => [
            
                    ],
                    "DELETE" => [
                        "description" => "Deletes the specified license.",
                        "parameters" => [
                            "id" => [
                                "type" => "integer|string",
                                "description" => "The license's ID. Value must be an integer or numeric string.",
                                "default" => NULL
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "id" => 3
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license/3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "id=3",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"id\":3}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "DELETE.bulk" => [
                        "description" => "Deletes the specified license.",
                        "parameters" => [
                            "ids" => [
                                "type" => "integer[]|string[]",
                                "description" => "The license's ID. Value must be an integer or numeric string.",
                                "default" => NULL
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "ids" => [
                                    3,
                                    4
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license?ids=[3,4]"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "ids=[3,4]",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"ids\":[3,4]}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "HEAD" => [
                        "description" => "Retrieves the specified license object and returns the headers the eventual GET response would have included.",
                        "parameters" => [
                            "id" => [
                                "type" => "integer|string",
                                "description" => "The license's ID. Value must be an integer or numeric string.",
                                "default" => NULL
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "id" => 3
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license/3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license?id=3"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "id=3",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"id\":3}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ],
                    "HEAD.bulk" => [
                        "description" => "Retrieves the set of license objects that satisfy the provided filters and returns the response the equivalent GET request would have generated without the body.",
                        "parameters" => [
                            "general" => [
                                "results_per_page" => [
                                    "type" => "integer",
                                    "description" => "Specifies the number of items to include per page.",
                                    "min" => 1,
                                    "max" => 50,
                                    "default" => 5
                                ],
                                "page_token" => [
                                    "type" => "string",
                                    "description" => "The value of a chosen property of the last sent item that can be used to fetch the next page of items with values equal to or beyond. This token should be either the \"next_token\" or \"prev_token\" provided by a previous request with the same parameters.",
                                    "default" => NULL
                                ],
                                "order_by" => [
                                    "type" => "string",
                                    "allowed_values" => "id,product_id,creation_timestamp,last_change_timestamp",
                                    "default" => "id"
                                ]
                            ],
                            "specific" => [
                                [
                                    "filters" => [
                                        "id" => [
                                            "type" => "integer|string",
                                            "description" => "The license's ID. Value must be an integer or numeric string if encoding a single ID. Multiple IDs may be encoded into a string by comma separating each ID.",
                                            "default" => NULL
                                        ],
                                        "id_min" => [
                                            "type" => "integer|string",
                                            "description" => "The minimum value for license ID. Value must be an integer or numeric string.",
                                            "default" => NULL
                                        ],
                                        "id_max" => [
                                            "type" => "integer|string",
                                            "description" => "The license's ID. Value must be an integer or numeric string.",
                                            "default" => NULL
                                        ],
                                        "etag" => [
                                            "type" => "string[]",
                                            "description" => "An array of ETags that the entities found with the specified filters must match. Has analogous meaning as the If-None-Match header specified in 3.1 of RFC 7232. If no entities match an etag in this filter, then no entities will be returned from this filter set. If an ETag is present in both this and the \"not_etag\" filter, then a 400 status code will be yielded.",
                                            "default" => NULL
                                        ],
                                        "not_etag" => [
                                            "type" => "string[]",
                                            "description" => "An array of ETags that the entities found with the specified filters must NOT match. Has analogous meaning as the If-None-Match header specified in 3.2 of RFC 7232. If any entity matches an etag in this filter, then that entity will not be returned from this filter set. If an ETag is present in both this and the \"etag\" filter, then a 400 status code will be yielded.",
                                            "default" => NULL
                                        ],
                                        "last_change_before" => [
                                            "type" => "integer|string",
                                            "description" => "The time before which matching entities must have last been modified.",
                                            "default" => NULL
                                        ],
                                        "last_change_after" => [
                                            "type" => "integer|string",
                                            "description" => "The time after which matching entities must have last been modified.",
                                            "default" => NULL
                                        ],
                                        "creation_before" => [
                                            "type" => "integer|string",
                                            "description" => "The time before which matching entities must have been created.",
                                            "default" => NULL
                                        ],
                                        "creation_after" => [
                                            "type" => "integer|string",
                                            "description" => "The time after which matching entities must have been created.",
                                            "default" => NULL
                                        ]
                                    ],
                                    "preconditions" => [
                                        "if_range" => [
                                            "type" => "integer|string|string[]",
                                            "description" => "A list of ETags or an epoch timestamp. If a list of ETags and the ETags of the entities found matches it, or else an epoch timestamp and the entities found have not been modified since it then the Range header provided will be satisfied, if no Range header is provided then this parameter will be ignored. Value must either be a string if an ETag is provided or an integer or numeric string if an epoch timestamp is being provided. Has analogous meaning to the If-Range header specified in 3.2 of RFC 7233.",
                                            "default" => NULL
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "valid_request_example" => [
                            "parameters" => [
                                "general" => [
                                    "results_per_page" => 10,
                                ],
                                "specific" => [
                                    [
                                        "filters" => [
                                            "id" => "3,4,5"
                                        ],
                                        "preconditions" => [
                                            "if_range" => 1473720896
                                        ]
                                    ],
                                    [
                                        "filters" => [
                                            "creation_before" => 1323749586,
                                            "not_etag" => [
                                                "aeofpd3ls",
                                                "apf84rkdp"
                                            ]
                                        ],
                                        "preconditions" => [
                                            "if_range" => 1473720896
                                        ]
                                    ]
                                ]
                            ],
                            "requests" => [
                                [
                                    "uri" => "https://www.example.com/license?general={\"results_per_page\":10}&data=[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "general={\"results_per_page\":10}&data=[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]",
                                    "content_type" => "application/x-www-form-urlencoded"
                                ],
                                [
                                    "uri" => "https://www.example.com/license",
                                    "body" => "{\"general\":{\"results_per_page\":10},\"data\":[{\"filters\":{\"id\":\"3,4,5\"},\"preconditions\":{\"if_range\":1473720896}},{\"filters\":{\"creation_before\":1323749586,\"not_etag\":[\"aeofpd3ls\",\"apf84rkdp\"]},\"preconditions\":{\"if_range\":1473720896}}]}",
                                    "content_type" => "application/json"
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            USER => [
    
            ],
            USERLICENSE => [
    
            ]
        ]
    ];

