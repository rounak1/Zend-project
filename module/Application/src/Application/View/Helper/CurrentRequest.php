<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CurrentRequest extends AbstractHelper {

    protected $params;
    protected $moduleName;
    protected $controllerName;
    protected $actionName;
    protected $routeName;

    public function __invoke() {
        $this->params = $this->initialize();
        return $this;
    }

    protected function initialize() {
        $params = array ();
        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();

        $router = $sm->get('router');

        $request = $sm->get('request');
        
       
        $matchedRoute = $router->match($request);
        if ($matchedRoute != NULL) {
            $params = $matchedRoute->getParams();
            /**
             * Controller are defined in two patterns.
             * 1. With Namespace
             * 2. Without Namespace.
             * Concatenate Namespace for controller without it.
             */
            $this->controllerName = !strpos($params['controller'], '\\') ?
                    $params['__NAMESPACE__'] . '\\' . $params['controller'] :
                    $params['controller'];
            $this->actionName = $params['action'];
            /**
             * Extract Module name from current controller name.
             * First camel cased character are assumed to be module name.
             */
            $this->moduleName = substr($this->controllerName, 0, strpos($this->controllerName, '\\'));
            $this->routeName = $matchedRoute->getMatchedRouteName();
        }

        return $params;
    }

    public function get($type) {
        $type = strtolower($type);
        $result = false;
        switch ($type) {
            case 'module':
                $result = $this->moduleName;
                break;
            case 'controller':
                $result = $this->controllerName;
                break;
            case 'action':
                $result = $this->actionName;
                break;
            case 'route':
                $result = $this->routeName;
                break;
        }
        return $result;
    }

}
