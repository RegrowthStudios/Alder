<?php
    return [
        "session_sources" => [
            USER_SESSION => [
                "type" => \Alder\Visitor\Visitor::COOKIE,
                "info_packet_classpath" => \Alder\Visitor\VisitorInfoPacket\UserSessionInfoPacket::class,
                "validators" => ["sub" => "user"],
            ]
        ]
    ];