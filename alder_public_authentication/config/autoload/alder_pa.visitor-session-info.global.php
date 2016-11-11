<?php
    return [
        "session_sources" => [
            USER_SESSION => [
                "prototype" => \Alder\PublicAuthentication\Visitor\Cookie\UserSessionCookie::class,
                "validators" => ["sub" => "user"],
                // TODO(Matthew): Think about how to handle defaults performantly.
            ]
        ]
    ];