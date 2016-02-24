<?php

/* 
 * Copyright (C) 2016 Matthew Marshall
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
    
    namespace Sycamore;
    
    return array (
        // TODO(Matthew): Get order write so api_XXX_index overwrites api_XXX.
        // TODO(Matthew): Investigate more dynamic route construction (vs. performance).
        "router" => array (
            "routes" => array (
                "api_user_index" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/user[/:action]",
                        "defaults" => array (
                            "controller" => "Sycamore\Controller\API\User\Index",
                            "action" => "index",
                        ),
                    ),
                ),
                "api_user" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/user[/:controller[/:action]]",
                        "defaults" => array (
                            "__NAMESPACE__" => "Sycamore\Controller\API\User",
                            "action" => "index",
                        ),
                    ),
                ),
                "api_newsletter_index" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/newsletter[/:action]",
                        "defaults" => array (
                            "controller" => "Sycamore\Controller\API\Newsletter\Index",
                            "action" => "index",
                        ),
                    ),
                ),
                "api_newsletter" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/newsletter[/:controller[/:action]]",
                        "defaults" => array (
                            "__NAMESPACE__" => "Sycamore\Controller\API\Newsletter",
                            "action" => "index",
                        ),
                    ),
                ),
                "api_attachment_index" => array (
                    "type" => "Zend\Mvc\Router\Http\Segment",
                    "options" => array (
                        "route" => "/api/attachment[/:action]",
                        "defaults" => array (
                            "controller" => "Sycamore\Controller\API\Attachment\Index",
                            "action" => "index",
                        ),
                    ),
                ),
            ),
        ),
        "controllers" => array (
            "invokables" => array (
                "Sycamore\Controller\API\User\Index" => "Sycamore\Controller\API\User\IndexController",
                "Sycamore\Controller\API\User\Ban" => "Sycamore\Controller\API\User\BanController",
                "Sycamore\Controller\API\Newsletter\Index" => "Sycamore\Controller\API\Newsletter\IndexController",
                "Sycamore\Controller\API\Newsletter\Subscriber" => "Sycamore\Controller\API\Newsletter\SubscriberController",
                "Sycamore\Controller\API\Attachment\Index" => "Sycamore\Controller\API\Attachment\IndexController",
            ),
        ),
    );
