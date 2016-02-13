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
    
    use Sycamore\Request;
    use Sycamore\Response;
    use Sycamore\Enums\ActionState;
    use Sycamore\Row\Route;

    /**
     * Sycamore dispatcher class.
     */
    class Dispatcher
    {
        /**
         * Creates a controller for the route, and then calls 
         * execute from within the controller passing in the request
         * and response.
         * 
         * @param \Sycamore\Route $route
         * @param \Sycamore\Request $request
         * @param \Sycamore\Response $response
         * 
         * @return mixed
         */
        public function dispatch(Route $route, Request& $request, Response& $response)
        {
            // Construct controller.
            $controller = $this->createController($request, $response, $route);
            
            // Get request method and force to lower case.
            $requestMethod = strtolower(filter_input(INPUT_SERVER, "REQUEST_METHOD"));
            
            // Construct action method name.
            $action = $requestMethod . "Action";
            
            // Fail if no action method exists by constructed name.
            if (!method_exists($controller, $action)) {
                return ActionState::FAILED;
            }
            
            // Trigger event to ensure permissions for action exist.
            $event = "preExecute" . ucfirst($requestMethod);
            if (!$this->eventManager->trigger($event, $controller)) {
                if (!Visitor::getInstance()->isLoggedIn) {
                    return ActionState::DENIED_NOT_LOGGED_IN;
                } else {
                    ErrorManager::addError("permission", "permission_missing");
                    $this->prepareExit();
                    return ActionState::DENIED;
                }
            }
            
            // Ultimately if everything else was successful,
            // return result of calling the action from the controller.
            return $controller->$action;
        }
  
        /**
         * Creates a new instance of the \Sycamore\Controller 
         * class stored in controllerClass.
         * 
         * @param \Sycamore\Request $request
         * @param \Sycamore\Resonse $response
         * @param \Sycamore\Route $route
         * 
         * @return \Sycamore\Controller\Controller
         */
        protected function createController(Request& $request, Response& $response, Route $route) {
            if ($request->getUriAsArray()[0] == "api") {
                $renderer = new \Sycamore\Renderer\JsonRenderer($response);
            } else {
                $renderer = new \Sycamore\Renderer\HtmlRenderer($response);
            }
            
            $controllerStr = $route->controller;
            return new $controllerStr($request, $response, $renderer);
        }
    }