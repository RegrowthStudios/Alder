<?php
    namespace Alder;
    
    use Zend\Mvc\Controller\AbstractRestfulController as ZendAbstractRestfulController;
    use Zend\Mvc\MvcEvent;
    
    /**
     * Extension of the Zend RESTful controller.
     * 
     * @author Matthew Marshall <matthew.marshall96@yahoo.co.uk>
     * @copyright 2016, Regrowth Studios Ltd. All Rights Reserved
     * @since 0.1.0
     * @abstract
     */
    abstract class AbstractRestfulController extends ZendAbstractRestfulController
    {
        /**
         * Contains data about the OPTIONS request method, generalised for all controllers.
         *
         * @var array
         */
        protected $preOptions = [
            "allow-header" => "OPTIONS",
            "body" => [
                "OPTIONS" => [
                    "description" => "Provides a breakdown of the options for interacting with this particular URI.",
                    "parameters" => [],
                ],
            ],
        ];
        
        /**
         * Should be populated with the data about the various allowed request methods for a given controller with basic documentation.
         *
         * @var array
         */
        protected $options = [];
        
        /**
         * Prepares documentation to be sent for an OPTION type request.
         * 
         * @return \Zend\Stdlib\ResponseInterface The response resulting from processing the request.
         */
        public function options()
        {
            $allowHeader = (isset($this->options["allow-header"])) ? $this->options["allow-header"] . "," . $this->preOptions["allow-header"] : $this->preOptions["allow-header"];
            $body = (isset($this->options["body"])) ? array_merge($this->options["body"], $this->preOptions["body"]) : $this->preOptions["body"];
            
            $this->response->setStatusCode(200);
            $this->response->getHeaders()->addHeaderLine("Allow", $allowHeader);
            $this->response->setContent($body);
            
            return $this->response;
        }
        
        /**
         * Attempts to see if a the locale was passed in either the URI or the
         * query string, returning it if found. Otherwise, returns the default locale.
         * 
         * @return string
         */
       protected function getRequestedLocale()
       {
           $locale = $this->event->getRouteMatch()->getParam("locale", false);
           if ($locale !== false) {
               return $locale;
           }

           $locale = $this->event->getRequest()->getQuery()->get("locale", false);
           if ($locale !== false) {
               return $locale;
           }

           return $this->plugins->get("config")["Alder"]["default_locale"];
       }
    
        /**
         * Handles dispatching the current request to the correct action.
         *
         * @param \Zend\Mvc\MvcEvent $event The event object for the dispatch event trigger.
         * 
         * @return mixed The result of processing the request.
         * 
         * @throws \Zend\Mvc\Exception\DomainException If no route matches in event or invalid HTTP method
         */
        public function onDispatch(MvcEvent $event)
        {
            // Grab route match and ensure it is valid.
            $routeMatch = $event->getRouteMatch();
            if (!$routeMatch) {
                throw new \DomainException('Missing route matches; unsure how to retrieve action.');
            }

            // Grab request.
            $request = $event->getRequest();

            // If an action is specified, perform that action.
            $action  = $routeMatch->getParam('action', false);
            if ($action) {
                // Handle arbitrary methods, ending in Action
                $method = static::getMethodFromAction($action);
                if (!method_exists($this, $method)) {
                    $method = 'notFoundAction';
                }
                $return = $this->$method();
                $event->setResult($return);
                return $return;
            }

            // If no action specified, then automatically run function corresponding to HTTP request method.
            $method = strtolower($request->getMethod());
            switch ($method) {
                // Custom HTTP methods (or custom overrides for standard methods)
                case (isset($this->customHttpMethodsMap[$method])):
                    $callable = $this->customHttpMethodsMap[$method];
                    $action = $method;
                    $return = call_user_func($callable, $event);
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
                    $id = $this->getIdentifier($routeMatch, $request);
                    $data = $this->processBodyContent($request);

                    if ($id !== false) {
                        $action = 'get';
                        $return = $this->get($id);
                        break;
                    }

                    $action = 'getList';
                    $return = $this->getList($data);
                    break;
                // HEAD
                case 'head':
                    $id = $this->getIdentifier($routeMatch, $request);
                    if ($id === false) {
                        $id = null;
                    }
                    $action = 'head';
                    $headResult = $this->head();
                    $response = ($headResult instanceof Response) ? clone $headResult : $event->getResponse();
                    $response->setContent('');
                    $return = $response;
                    break;
                // OPTIONS
                case 'options':
                    $action = 'options';
                    $this->options();
                    $return = $event->getResponse();
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
                    $response = $event->getResponse();
                    $response->setStatusCode(405);
                    return $response;
            }

            $routeMatch->setParam('action', $action);
            $event->setResult($return);
            return $return;
        }
    }
