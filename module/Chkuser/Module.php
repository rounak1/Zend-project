<?php
namespace Chkuser;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

// Add these import statements:
 /*use Chkuser\Model\User;
 use Chkuser\Model\UserMapper;
 use Application\Model\Country;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
  * 
  */
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;


class Module
{
     
     public function onBootstrap(Event $e){
           
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
     {
         
        return array(
            'factories' => array(
                    'UserMapper' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $mapper = new UserMapper($dbAdapter);
                        return $mapper; 
                    },
                    'EloquentZF2Adapter' => function ($sm) {
                        $eloquentZF2Adapter = $sm->get('EloquentZF2');
                        return $eloquentZF2Adapter; 
                    },

                    'Chkuser\Model\MyAuthStorage' => function($sm){
                        return new \Chkuser\Model\MyAuthStorage('chkauth_storage'); 
                    },

                    'AuthService' => function($sm) {
                        $auth = new \Zend\Authentication\AuthenticationService();
                        $dbAdapter= $sm->get('Zend\Db\Adapter\Adapter');
                        $dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter,
                                              'chk_users','user_name','password', 'MD5(?)AND user_status = "active"');
                        $authService = new AuthenticationService();
                        $authService->setAdapter($dbTableAuthAdapter);
                        $authService->setStorage($sm->get('Chkuser\Model\MyAuthStorage'));

                        return $authService;
                     },   

                    'CountryService' => function($sm) {
                         $country = new \Application\Model\Country();
                        return $country;
                    }
                    
                    
                ),
                
                
                
        );

     }
     
    

}
