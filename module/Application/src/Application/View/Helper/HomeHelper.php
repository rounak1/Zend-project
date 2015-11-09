<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class HomeHelper extends AbstractHelper 
{
    public function __invoke() {
        
        $this->params = $this->initialize();
        return $this->params;
    }
    
    protected function initialize() {
        
        $params = array ();
        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
        
        $EloquentDb = $sm
                ->get('EloquentZF2Adapter');

        //$session_data = $sm->get('AuthService')->getStorage()->read();
       
        $data = array();
        
        $data['division'] = $EloquentDb::table('chk_division')
                ->select('id','name')
                ->orderBy('chk_division.name','asc')
                ->get();
        
        $data['total_jobs'] = $EloquentDb::table('chk_jobs')
                ->where('chk_jobs.posting_date', '<=', date("Y-m-d"))
                ->where('chk_jobs.job_deadline', '>=', date("Y-m-d"))
                ->count();
        
        /*$total_active_job_count = \Admin\Model\Job::where('employer_id', '=', $auth_users->id)
                ->where('chk_jobs.posting_date', '<=', date("Y-m-d"))
                ->where('chk_jobs.job_deadline', '>=', date("Y-m-d"))
                ->count();*/
        
        
        return $data;
    }    
    
}

