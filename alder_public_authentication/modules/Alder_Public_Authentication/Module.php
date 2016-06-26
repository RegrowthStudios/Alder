<?php

    namespace Alder_Public_Authentication;
    
    use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
    use Zend\ModuleManager\Feature\ConfigProviderInterface;
    use Zend\Mvc\Application;
    use Zend\Mvc\ModuleRouteListener;
    use Zend\Mvc\MvcEvent;
    
    /**
     * Module class for Sycamore.
     */
    class Module implements AutoloaderProviderInterface, ConfigProviderInterface
    {
        /**
         * Initialises listeners during the bootstrap process.
         *
         * @param \Zend\Mvc\MvcEvent $e
         */
        public function onBootstrap(MvcEvent $e)
        {
            $eventManager = $e->getApplication()->getEventManager();
            $moduleRouteListener = new ModuleRouteListener();
            $moduleRouteListener->attach($eventManager);

            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,  [$this, "onDispatchError"], 10);
        }

        // TODO(Matthew): Use a language object for strings presented to user.
        /**
         * Returns a JSON model of the dispatch error in the provided event. Returns void if no error exists.
         *
         * @param \Zend\Mvc\MvcEvent $e
         *
         * @return mixed
         */
        public function onDispatchError(MvcEvent $e)
        {
            $e->stopPropagation();
            $response = $e->getResponse();
            $exception = $e->getParam("exception");
            $error = $e->getError();
            
            if ($error === Application::ERROR_ROUTER_NO_MATCH) {
                $response->setStatusCode(404);
                $response->setContent(API::encode(["error" => "No route match was found for the given URI."]));
            } else if ($error === Application::ERROR_CONTROLLER_NOT_FOUND
                    || $error === Application::ERROR_CONTROLLER_INVALID
                    || $error === Application::ERROR_CONTROLLER_CANNOT_DISPATCH
                    || $error === Application::ERROR_EXCEPTION) {
                if ($exception) {
                    $e->getApplication()->getServiceManager()->get("Logger")->crit($exception);
                }
                $response->setStatusCode(500);
                if (ENV != PRODUCTION) {
                    $response->setContent(API::encode(["error" => "A critical error: \n    " . $error . "\nhas occurred in processing this request."]));
                } else {
                    $response->setContent(API::encode(["error" => "A critical error has occurred in processing this request. Please contact the service provider if this persists."]));
                }
            } else {
                $response->setStatusCode(500);
                $response->setContent(API::encode(["error" => "An unknown critical error has occurred. Please contact the service provider if this persists."]));
            }
            return $response;
        }

        /**
         * Returns an array of configuration options for the module.
         *
         * @return array
         */
        public function getConfig()
        {
            return include file_build_path(ALDER_MODULE_DIRECTORY, "config", "module.config.php");
        }

        /**
         * Returns an array of configuration options for the autoloader for this module.
         *
         * @return array
         */
        public function getAutoloaderConfig()
        {
            return [
                "Zend\Loader\StandardAutoloader" => [
                    "namespaces" => [
                        "Alder" => file_build_path(ALDER_MODULE_DIRECTORY, "src", "Alder_Public_Authentication"),
                    ]
                ]
            ];
        }
    }