<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class JobHelper extends AbstractHelper 
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
        
        $EloquentDb = $sm
                ->get('EloquentZF2Adapter');

        $session_data = $sm->get('AuthService')->getStorage()->read();
       
        $findJobs = array();
        $findJobs = $EloquentDb::table('chk_job_category')
                ->where('status','=','1')->orderBy('name', 'asc')->get();
       
      
        return $findJobs;        
        
    }

}
