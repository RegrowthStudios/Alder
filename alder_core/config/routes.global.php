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
                    new \Alder\Admin\Action\DashboardAction()
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/demographics",
                "middleware" => [
                    new \Alder\Admin\Action\DemographicsAction()
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/api",
                "middleware" => [
                    new \Alder\Admin\Action\APIStatisticsAction()
                ]
            ],
            [
                "name" => "settings",
                "path" => "/admin/settings",
                "middleware" => [
                    new \Alder\Admin\Action\SettingsAction()
                ]
            ],
            [
                "name" => "tasks",
                "path" => "/admin/tasks",
                "middleware" => [
                    new \Alder\Admin\Action\TasksAction()
                ]
            ],
            [
                "name" => "cache-rebuilder",
                "path" => "/admin/cache-rebuilder",
                "middleware" => [
                    new \Alder\Admin\Action\CacheRebuilderAction()
                ]
            ],
            [
                "name" => "file-verifier",
                "path" => "/admin/file-verifier",
                "middleware" => [
                    new \Alder\Admin\Action\FileVerifierAction()
                ]
            ],
            [
                "name" => "action-log",
                "path" => "/admin/action-log",
                "middleware" => [
                    new \Alder\Admin\Action\ActionLogAction()
                ]
            ],
            [
                "name" => "server-error-log",
                "path" => "/admin/server-error-log",
                "middleware" => [
                    new \Alder\Admin\Action\ServerErrorLogAction()
                ]
            ],
            [
                "name" => "alerts",
                "path" => "/admin/alerts",
                "middleware" => [
                    new \Alder\Admin\Action\AlertsAction()
                ]
            ],
            [
                "name" => "messages",
                "path" => "/admin/messages",
                "middleware" => [
                    new \Alder\Admin\Action\MessagesAction()
                ]
            ],
            [
                "name" => "install",
                "path" => "/admin/install",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallAction()
                ]
            ],
            [
                "name" => "install-config",
                "path" => "/admin/install/config",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallConfigAction()
                ]
            ],
            [
                "name" => "install-database",
                "path" => "/admin/install/database",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallDatabaseAction()
                ]
            ],
            [
                "name" => "install-tasks",
                "path" => "/admin/install/tasks",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallTasksAction()
                ]
            ],
            [
                "name" => "install-complete",
                "path" => "/admin/install/complete",
                "middleware" => [
                    new \Alder\Admin\Install\Action\InstallCompleteAction()
                ]
            ]
        ]
    ];
