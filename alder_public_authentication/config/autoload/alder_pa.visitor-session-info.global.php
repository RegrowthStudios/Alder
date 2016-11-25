<?php
    return [
        "alder" => [
            "public_authentication" => [
                "session" => [
                    "sources" => [
                        USER_SESSION => [
                            "type" => \Alder\Visitor\Visitor::COOKIE,
                            "info_packet_classpath" => \Alder\Visitor\VisitorInfoPacket\UserSessionInfoPacket::class,
                            "validators" => ["sub" => "user"],
                        ]
                    ]
                ]
            ]
        ]
    ];