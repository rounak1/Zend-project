<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class CustomRouteHelper extends AbstractHelper implements ServiceLocatorAwareInterface {

    protected $matchedRoute = null;  
   

/**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    
    public function getMatchRoute()  
    {  
        if ($this->matchedRoute === null) {  
            // for example, get the default value from app config  
            $sl= $this->getServiceLocator()->getServiceLocator();
            $sm  = $sl->get('application')->getServiceManager();
            $router = $sm->get('router');
            $request = $sm->get('request');
            
            $this->matchedRoute = $router->match($request);
                
              
        }  
        return $this->matchedRoute;  
    }  
    
    public function __invoke()  
    {  
        $matchedRoute = $this->getMatchRoute();  
        return $matchedRoute;  
    }  

}
