<?php

    return [
        "oauth" => [
            "invalid_grant_type" => "The grant type specified in the request is not valid. Must be one of: %s.",
            "missing_client_id" => "A client ID was not supplied in the request.",
            "invalid_client_id" => "The client ID supplied was not matched to any registered client.",
            "invalid_client_secret" => "The client secret supplied is not valid.",
            "only_confidential_clients" => "Only confidential clients can make a request for an access token to their own information. To edit a registered public client's details, log in with an appropriately privileged user.",
            "invalid_redirect_uri" => "The redirect URI supplied was not matched to any registered redirect URIs of the requesting client.",
            "missing_auth_code" => "An authorisation code was not supplied in the request.",
            "invalid_auth_code" => "The authorisation code supplied is not valid.",
            "invalid_user_credentials" => "The user credentials supplied are not valid.",
        ],
        1010101 => "Password not provided.",
        1010102 => "Neither a username or password was provided.",
    ];
