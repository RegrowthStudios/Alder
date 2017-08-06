<?php
    
    /**
     * Route configuration for the application.
     */
    // TODO(Matthew): Do paths need to incorporate "/admin" or not?

    return [
        "routes" => [
            [
                "name" => "dashboard",
                "path" => "/",
                "middleware" => [
                    new \Alder\Install\Action\DashboardAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/stats/demographics",
                "middleware" => [
                    new \Alder\Install\Action\DemographicsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/stats/api",
                "middleware" => [
                    new \Alder\Install\Action\APIStatisticsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "settings",
                "path" => "/settings",
                "middleware" => [
                    new \Alder\Install\Action\SettingsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "tasks",
                "path" => "/tasks",
                "middleware" => [
                    new \Alder\Install\Action\TasksAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "cache-rebuilder",
                "path" => "/cache-rebuilder",
                "middleware" => [
                    new \Alder\Install\Action\CacheRebuilderAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "file-verifier",
                "path" => "/file-verifier",
                "middleware" => [
                    new \Alder\Install\Action\FileVerifierAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "action-log",
                "path" => "/action-log",
                "middleware" => [
                    new \Alder\Install\Action\ActionLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "server-error-log",
                "path" => "/server-error-log",
                "middleware" => [
                    new \Alder\Install\Action\ServerErrorLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "alerts",
                "path" => "/alerts",
                "middleware" => [
                    new \Alder\Install\Action\AlertsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "messages",
                "path" => "/messages",
                "middleware" => [
                    new \Alder\Install\Action\MessagesAction([ "module" => "core" ])
                ]
            ],
        ]
    ];
