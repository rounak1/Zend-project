<?php

namespace Employer;

use Zend\Mvc\MvcEvent;

class Module {

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach('route', array($this, 'EmployerCheck'), 2);
    }

    public function EmployerCheck(MvcEvent $e) {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        $router = $sm->get('router');
        $request = $sm->get('request');
        $matchedRoute = $router->match($request);

        if (null !== $matchedRoute) {
            $controller = $matchedRoute->getParam('controller');
            $action = $matchedRoute->getParam('action');
            $module_array = explode('\\', $controller);
            $module = $module_array[0];
            // check auth...
            $response = $e->getResponse();
            if ($module == 'Employer') {
                // to do     
                if (!strpos($controller, "Registration")) 
                {

                    
                        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
                        $sm = $e->getApplication()->getServiceManager();
                        $controller = $e->getTarget();
                        $controller->layout('layout/employer');
                         $router = $sm->get('router');
                        $request = $sm->get('request');
                        // check auth...
                        $response = $e->getResponse();

                        if (!$sm->get('AuthService')->hasIdentity()) {
                            //$controller->plugin('redirect')->toRoute('login');
                             return $controller->redirect()->toRoute('login');
                            
                        }
                        $users = $sm->get('AuthService')->getStorage()->read();
                        $configs = $sm->get('config');
                        
                        if ($users->user_type != $configs['config_user_type']['employer']) {
                            /*$url = $router->assemble(array(), array('name' => 'login'));
                            $response->setStatusCode(302);
                            $response->getHeaders()->addHeaderLine('Location', $url);*/
                             return $controller->redirect()->toRoute('login');
                        }
                    }, 100);
                }
            }
        }
    }

}
