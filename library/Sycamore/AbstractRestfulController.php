<?php

/* 
 * Copyright (C) 2016 Matthew Marshall <matthew.marshall96@yahoo.co.uk>
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
    
    use Zend\Mvc\Controller\AbstractRestfulController;
    
    /**
     * Extension of the Zend RESTful controller.
     */
    class AbstractRestfulController extends AbstractRestfulController
    {
        /**
         * Contains data about the OPTIONS request method, generalised for all controllers.
         *
         * @var array
         */
        protected $preOptions = array (
            "allow-header" => "OPTIONS",
            "body" => array (
                "OPTIONS" => array (
                    "description" => "Provides a breakdown of the options for interacting with this particular URI.",
                ),
            ),
        );
        
        /**
         * Should be populated with the data about the various allowed request methods for a given controller with basic documentation.
         *
         * @var array
         */
        protected $options = array ();
        
        /**
         * Prepares and sends the OPTIONS header & body for the given controller instance.
         */
        protected function options()
        {
            $allowHeader = (isset($this->options["allow-header"])) ? $this->options["allow-header"] . "," . $this->preOptions["allow-header"] : $this->preOptions["allow-header"];
            $body = (isset($this->options["body"])) ? array_merge($this->options["body"], $this->preOptions["body"]) : $this->preOptions["body"];
            
            $this->response->setStatusCode(200);
            $this->response->getHeaders()->addHeaderLine("Allow", $allowHeader);
            $this->response->setContent($body);
        }
        
        /**
         * Handle the current request.
         *
         * @param  \Zend\Mvc\MvcEvent $e
         * 
         * @return mixed
         * @throws \Zend\Mvc\Exception\DomainException if no route matches in event or invalid HTTP method
         */
        public function onDispatch(\Zend\Mvc\MvcEvent $e)
        {
            // Grab route match and ensure it is valid.
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                throw new Exception\DomainException('Missing route matches; unsure how to retrieve action.');
            }

            // Grab request.
            $request = $e->getRequest();

            // If an action is specified, perform that action.
            $action  = $routeMatch->getParam('action', false);
            if ($action) {
                // Handle arbitrary methods, ending in Action
                $method = static::getMethodFromAction($action);
                if (!method_exists($this, $method)) {
                    $method = 'notFoundAction';
                }
                $return = $this->$method();
                $e->setResult($return);
                return $return;
            }

            // If no action specified, then automatically run function corresponding to HTTP request method.
            $method = strtolower($request->getMethod());
            switch ($method) {
                // Custom HTTP methods (or custom overrides for standard methods)
                case (isset($this->customHttpMethodsMap[$method])):
                    $callable = $this->customHttpMethodsMap[$method];
                    $action = $method;
                    $return = call_user_func($callable, $e);
                    break;
                // DELETE
                case 'delete':
                    $id = $this->getIdentifier($routeMatch, $request);
                    $data = $this->processBodyContent($request);

                    if ($id !== false) {
                        $action = 'delete';
                        $return = $this->delete($id);
                        break;
                    }

                    $action = 'deleteList';
                    $return = $this->deleteList($data);
                    break;
                // GET
                case 'get':
                    $action = 'get';
                    $return = $this->get();
                    break;
                // HEAD
                case 'head':
                    $action = 'head';
                    $headResult = $this->head();
                    $response = ($headResult instanceof Response) ? clone $headResult : $e->getResponse();
                    $response->setContent('');
                    $return = $response;
                    break;
                // OPTIONS
                case 'options':
                    $action = 'options';
                    $this->options();
                    $return = $e->getResponse();
                    break;
                // PATCH
                case 'patch':
                    $id = $this->getIdentifier($routeMatch, $request);
                    $data = $this->processBodyContent($request);

                    if ($id !== false) {
                        $action = 'patch';
                        $return = $this->patch($id, $data);
                        break;
                    }

                    $action = 'patchList';
                    $return = $this->patchList($data);
                    break;
                // POST
                case 'post':
                    $action = 'create';
                    $return = $this->processPostData($request);
                    break;
                // PUT
                case 'put':
                    $id   = $this->getIdentifier($routeMatch, $request);
                    $data = $this->processBodyContent($request);

                    if ($id !== false) {
                        $action = 'replace';
                        $return = $this->replace($id, $data);
                        break;
                    }

                    $action = 'replaceList';
                    $return = $this->replaceList($data);
                    break;
                // All others...
                default:
                    $response = $e->getResponse();
                    $response->setStatusCode(405);
                    return $response;
            }

            $routeMatch->setParam('action', $action);
            $e->setResult($return);
            return $return;
        }
    }
