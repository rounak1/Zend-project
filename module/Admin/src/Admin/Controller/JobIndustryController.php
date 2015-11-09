<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobIndustryController extends AbstractActionController{
    public function indexAction(){
        //$indutryType = new \Admin\Model\JobIndustry;
        //$results = $indutryType::where('status','=','1')->get();
        
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
        
        $results =  $EloquentDb::table('chk_job_industry')
        ->select('chk_job_industry.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_job_industry.modified_by', '=', 'chk_users.id')
        ->where('chk_job_industry.status','=', '1')
        ->orderBy('name','ASC')
        ->get();

        return new ViewModel(array('industryTypes' => $results));        
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $jcat = new \Admin\Model\JobIndustry();

        if ($request->isPost()) {
            $jobIndustry = $jcat->find($id);
            $jobIndustry->name = $request->getPost('jcname');
            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $jobIndustry->modified_by = $auth_users->id;
            $jobIndustry->save();
            $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
            return $this->redirect()->toUrl("/admin/Jobindustry");
        }

        $results = $jcat->find($id);

        return new ViewModel(array('jobIndustry' => $results));
    }
    
    public function addAction() {
        $error_messages   = array();
        $request          = $this->getRequest();
        $post_data        = $request->getPost();
        $jobIndustry = new \Admin\Model\JobIndustry();

        if ($request->isPost()) {
            if ($jobIndustry::where(array('name'=>$request->getPost('jcname'),'status'=>1))->first()) {
                $error_messages[]="This Industrial Category already exists.";
            }else{
                $jobIndustry->name = $request->getPost('jcname');
                $auth_users =  $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $jobIndustry->created_by = $auth_users->id;
                $jobIndustry->save();            
                $this->flashMessenger()->setNamespace('success')->addMessage('Job Industry has been added.');
                return $this->redirect()->toUrl("/admin/Jobindustry");
            }
        }
        
        return new ViewModel(array(
            'error_messages'   => $error_messages,
            'post_data'        => $post_data 
        ));
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        $jobIndustries = new \Admin\Model\JobIndustry();

        $jobIndustry = $jobIndustries->find($id);
        $jobIndustry->status = 0;
        $jobIndustry->save();
        
        $this->flashMessenger()->setNamespace('success')->addMessage('Job Indutry has been deleted.');

        return $this->redirect()->toUrl("/admin/Jobindustry");
    }
}