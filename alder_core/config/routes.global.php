<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        "routes" => [
            [
                "name" => "dashboard",
                "path" => "/admin",
                "middleware" => [
                    new \Alder\Admin\Action\DashboardAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/demographics",
                "middleware" => [
                    new \Alder\Admin\Action\DemographicsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/api",
                "middleware" => [
                    new \Alder\Admin\Action\APIStatisticsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "settings",
                "path" => "/admin/settings",
                "middleware" => [
                    new \Alder\Admin\Action\SettingsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "tasks",
                "path" => "/admin/tasks",
                "middleware" => [
                    new \Alder\Admin\Action\TasksAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "cache-rebuilder",
                "path" => "/admin/cache-rebuilder",
                "middleware" => [
                    new \Alder\Admin\Action\CacheRebuilderAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "file-verifier",
                "path" => "/admin/file-verifier",
                "middleware" => [
                    new \Alder\Admin\Action\FileVerifierAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "action-log",
                "path" => "/admin/action-log",
                "middleware" => [
                    new \Alder\Admin\Action\ActionLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "server-error-log",
                "path" => "/admin/server-error-log",
                "middleware" => [
                    new \Alder\Admin\Action\ServerErrorLogAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "alerts",
                "path" => "/admin/alerts",
                "middleware" => [
                    new \Alder\Admin\Action\AlertsAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "messages",
                "path" => "/admin/messages",
                "middleware" => [
                    new \Alder\Admin\Action\MessagesAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install",
                "path" => "/admin/install",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-config",
                "path" => "/admin/install/config",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallConfigAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-database",
                "path" => "/admin/install/database",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallDatabaseAction([ "module" => "core" ])
                ]
            ],
            [
                "name" => "install-tasks",
                "path" => "/admin/install/tasks",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallTasksAction([ "module" => "core" ])
                ]
            ]
        ]
    ];
