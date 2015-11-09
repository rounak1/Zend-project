<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class JobCircularTypeController extends AbstractActionController{
    public function indexAction(){
        //$circularType = new \Admin\Model\JobCircularType;
        //$results = $circularType::where('status','=','1')->get();

        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_job_circular')
        ->select('chk_job_circular.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_job_circular.modified_by', '=', 'chk_users.id')
        ->where('chk_job_circular.status','=', '1')
        ->orderBy('updated_at','DESC')
        ->get();

        return new ViewModel(array('circularTypes' => $results));
    }

    public function editAction(){
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $JobCircularTypes = new \Admin\Model\JobCircularType();

        if ($request->isPost()) {
            $JobCircularType = $JobCircularTypes->find($id);
            $auth_users =  $this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $JobCircularType->modified_by = $auth_users->id;
            $JobCircularType->type = $request->getPost('jsname');
            $JobCircularType->save();
            $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
            return $this->redirect()->toUrl("/admin/circularType");
        }

        $results = $JobCircularTypes->find($id);

        return new ViewModel(array('circularTypes' => $results));
    }

    public function addAction(){
        $error_messages   = array();
        $request          = $this->getRequest();
        $post_data        = $request->getPost();
        $JobCircularType  = new \Admin\Model\JobCircularType();

        if ($request->isPost()) {
            if ($JobCircularType::where(array('type'=>$request->getPost('jsname'),'status'=>1))->first()) {
                $error_messages[]="This Job Circular Type already exists.";
            }else{
                $JobCircularType->type = $request->getPost('jsname');
                $auth_users =  $this->getServiceLocator()->get('AuthService')->getStorage()->read();            
                $JobCircularType->created_by = $auth_users->id;
                $JobCircularType->save();            
                $this->flashMessenger()->setNamespace('success')->addMessage('Job Circular Type has been added.');
                return $this->redirect()->toUrl("/admin/circularType");
            }
        }

        return new ViewModel(array(
            'error_messages' => $error_messages,
            'post_data'      => $post_data
        ));
    }

    public function deleteAction(){
        $id = $this->params()->fromRoute('id');
        $JobCircularTypes = new \Admin\Model\JobCircularType();

        $JobCircularType = $JobCircularTypes->find($id);
        $JobCircularType->status = 0;
        $JobCircularType->save();
        
        $this->flashMessenger()->setNamespace('success')->addMessage('Job Circular type has been deleted.');
        return $this->redirect()->toUrl("/admin/circularType");    
    }
}