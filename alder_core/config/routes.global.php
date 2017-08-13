<?php
    
    /**
     * Route configuration for the application.
     */

    return [
        "routes" => [
            [
                "name" => "login",
                "path" => "/admin/login",
                "middleware" => [
                    new \Alder\Action\Admin\LoginAction()
                ]
            ],
            [
                "name" => "dashboard",
                "path" => "/admin",
                "middleware" => [
                    new \Alder\Action\Admin\DashboardAction()
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/demographics",
                "middleware" => [
                    new \Alder\Action\Admin\DemographicsAction()
                ]
            ],
            [
                "name" => "demographics",
                "path" => "/admin/stats/api",
                "middleware" => [
                    new \Alder\Action\Admin\APIStatisticsAction()
                ]
            ],
            [
                "name" => "settings",
                "path" => "/admin/settings",
                "middleware" => [
                    new \Alder\Action\Admin\SettingsAction()
                ]
            ],
            [
                "name" => "tasks",
                "path" => "/admin/tasks",
                "middleware" => [
                    new \Alder\Action\Admin\TasksAction()
                ]
            ],
            [
                "name" => "cache-rebuilder",
                "path" => "/admin/cache-rebuilder",
                "middleware" => [
                    new \Alder\Action\Admin\CacheRebuilderAction()
                ]
            ],
            [
                "name" => "file-verifier",
                "path" => "/admin/file-verifier",
                "middleware" => [
                    new \Alder\Action\Admin\FileVerifierAction()
                ]
            ],
            [
                "name" => "action-log",
                "path" => "/admin/action-log",
                "middleware" => [
                    new \Alder\Action\Admin\ActionLogAction()
                ]
            ],
            [
                "name" => "server-error-log",
                "path" => "/admin/server-error-log",
                "middleware" => [
                    new \Alder\Action\Admin\ServerErrorLogAction()
                ]
            ],
            [
                "name" => "alerts",
                "path" => "/admin/alerts",
                "middleware" => [
                    new \Alder\Action\Admin\AlertsAction()
                ]
            ],
            [
                "name" => "messages",
                "path" => "/admin/messages",
                "middleware" => [
                    new \Alder\Action\Admin\MessagesAction()
                ]
            ],
            [
                "name" => "install",
                "path" => "/admin/install",
                "middleware" => [
                    new \Alder\Action\Admin\InstallAction()
                ]
            ],
            [
                "name" => "install-config",
                "path" => "/admin/install/config",
                "middleware" => [
                    new \Alder\Action\Admin\InstallConfigAction()
                ]
            ],
            [
                "name" => "install-database",
                "path" => "/admin/install/database",
                "middleware" => [
                    new \Alder\Action\Admin\InstallDatabaseAction()
                ]
            ],
            [
                "name" => "install-tasks",
                "path" => "/admin/install/tasks",
                "middleware" => [
                    new \Alder\Action\Admin\InstallTasksAction()
                ]
            ],
            [
                "name" => "install-complete",
                "path" => "/admin/install/complete",
                "middleware" => [
                    new \Alder\Action\Admin\InstallCompleteAction()
                ]
            ]
        ]
    ];
