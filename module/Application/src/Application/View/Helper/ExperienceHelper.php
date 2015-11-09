<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class ExperienceHelper extends AbstractHelper 
{
    public function __invoke($user_id) {
        $this->params = $this->initialize($user_id);
        return $this->params;
    }
    
    protected function initialize($user_id) {
        
        //echo "here"; 
        $params = array ();
        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();
        
        $EloquentDb = $sm
                ->get('EloquentZF2Adapter');

        $session_data = $sm->get('AuthService')->getStorage()->read();
       
        $data = array();
        
        $data['last_experience'] = $EloquentDb::table('chk_cv_experience')
                ->select('chk_cv_experience.*')
                ->where('chk_cv_experience.user_id', $user_id)
                ->orderBy('chk_cv_experience.date_from','desc')
                ->first();
        
        $data['last_education'] = $EloquentDb::table('chk_cv_education')
                ->join ('chk_institute','chk_cv_education.institute_id','=','chk_institute.id')
                ->join ('chk_education_degree','chk_cv_education.degree_id','=','chk_education_degree.id')
                ->where('chk_cv_education.user_id', $user_id)
                ->select('chk_education_degree.*','chk_institute.name as institute_name','chk_education_degree.name as degree_name')
                ->orderBy('chk_cv_education.year_pass','desc')
                ->first();
        
        $data['is_block'] = $EloquentDb::table('chk_company_block')
                ->select('chk_company_block.id')
                ->where('chk_company_block.user_id',$user_id)
                ->where('chk_company_block.company_id',$session_data->id)
                //->orderBy('chk_cv_education.year_pass','desc')
                ->first();
        
        return $data;
    }    
    
}