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
                    new \Alder\Admin\Action\DashboardAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/stats/demographics",
                "middleware" => [
                    new \Alder\Admin\Action\DemographicsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/stats/api",
                "middleware" => [
                    new \Alder\Admin\Action\APIStatisticsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "settings",
                "path" => "/settings",
                "middleware" => [
                    new \Alder\Admin\Action\SettingsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "tasks",
                "path" => "/tasks",
                "middleware" => [
                    new \Alder\Admin\Action\TasksAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "cache-rebuilder",
                "path" => "/cache-rebuilder",
                "middleware" => [
                    new \Alder\Admin\Action\CacheRebuilderAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "file-verifier",
                "path" => "/file-verifier",
                "middleware" => [
                    new \Alder\Admin\Action\FileVerifierAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "action-log",
                "path" => "/action-log",
                "middleware" => [
                    new \Alder\Admin\Action\ActionLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "server-error-log",
                "path" => "/server-error-log",
                "middleware" => [
                    new \Alder\Admin\Action\ServerErrorLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "alerts",
                "path" => "/alerts",
                "middleware" => [
                    new \Alder\Admin\Action\AlertsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "messages",
                "path" => "/messages",
                "middleware" => [
                    new \Alder\Admin\Action\MessagesAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install",
                "path" => "/install",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-config",
                "path" => "/install/config",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallConfigAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-database",
                "path" => "/install/database",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallDatabaseAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-tasks",
                "path" => "/install/tasks",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallTasksAction([ "module" => "core" ])
                ]
            ]
        ]
    ];
