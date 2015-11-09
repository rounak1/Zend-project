<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class PermissionHelper extends AbstractHelper 
{

    protected $params;
    
    public function __invoke() {
       // $this->params = 
        return $this->initialize();
    }
    
    protected function initialize() {
        
        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
        
        $EloquentDb = $sm
                ->get('EloquentZF2Adapter');

       // $session_data = $sm->get('AuthService')->getStorage()->read();

        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
        
        
        $session_data = $sm->get('AuthService')->getStorage()->read();
       
        
        $chk_roles =  $EloquentDb::table('chk_roles')
                        ->select('chk_permissions.perm_name')
                        ->leftJoin('chk_role_permissions','chk_role_permissions.role_id','=', 'chk_roles.id')
                        ->leftJoin('chk_permissions','chk_permissions.id','=', 'chk_role_permissions.perm_id')                           
                        // $session_data->role
                        ->where ('chk_roles.role_name','=',$session_data->user_role)
                        //->where ('chk_roles.role_name','=','publisher')
                        
                        ->get();
        
        
        foreach ($chk_roles as $value)
        {
            $perm_roles[] =  $value['perm_name'];
        }
        
       
        
        
      
        return  $perm_roles;        
        
    }

}
