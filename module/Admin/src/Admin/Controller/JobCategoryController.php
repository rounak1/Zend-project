<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class JobCategoryController extends AbstractActionController
{

    public function indexAction(){
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_job_category')
        ->select('chk_job_category.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_job_category.modified_by', '=', 'chk_users.id')
        ->where('chk_job_category.status','=', '1')
        ->orderBy('updated_at','DESC')
        ->get();

        return new ViewModel(array('jcategorys' => $results));
    }
    
    public function editAction() {
//        $error_messages=array();
//        $success_messages=array();
        $request=$this->getRequest();
        $id=$this->params()->fromRoute('id');
        $jcat=new \Admin\Model\JobCategory();

        if($request->isPost()){
//            if ($jcat::where('name', '=', $request->getPost('jname'))->first()) {
//                $this->flashMessenger()->setNamespace('success')->addMessage('This Business Type Already Exist.');
//                return $this->redirect()->toUrl("/admin/jcategory/edit/$id");
//            }
//            else{
                $jobCategory = $jcat->find($id);
                $jobCategory->name = $request->getPost('jname');
                $auth_users =  $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $jobCategory->modified_by = $auth_users->id;
                $jobCategory->save();
                $this->flashMessenger()->setNamespace('success')->addMessage('Update Successful');
                return $this->redirect()->toUrl("/admin/jcategory");
//            }
        }

        $results = $jcat->find($id);

        return new ViewModel(
                array(
//                    'error_messages'=>$error_messages,
//                    'success_messages'=>$success_messages,
                    'jcats'=>$results
                )
        );
    }

    public function addAction() {
        $error_messages   = array();
        $request          = $this->getRequest();
        $post_data        = $request->getPost();
        $jcat = new \Admin\Model\JobCategory();

        if ($request->isPost()){
            if ($jcat::where('name', '=', $request->getPost('jname'))->first()) {
                $error_messages[]="This Functional Category Already Exists.";
            }else{
                $jcat->name = $request->getPost('jname');
                $auth_users =  $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $jcat->created_by = $auth_users->id;
                //$btype->uid = 0;
                // $btype->name =
                $jcat->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Functional Category has been added.');
                return $this->redirect()->toUrl("/admin/jcategory");
            }
        }

        return new ViewModel(array(
            'error_messages' => $error_messages,
            'post_data' => $post_data
        ));
    }

    public function deleteAction() {
        //$this->layout('layout/admin'); 

        $id = $this->params()->fromRoute('id');
        $jcat = new \Admin\Model\JobCategory();

        $jobCategory = $jcat->find($id);
        $jobCategory->status = 0;
        $jobCategory->save();
        
        $this->flashMessenger()->setNamespace('success')->addMessage('Job Category has been deleted.');
        return $this->redirect()->toUrl("/admin/jcategory");
    }

    public function isCategoryExists($cat=""){
        $cat_exists=false;
        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');

        $validator=new \Zend\Validator\Db\RecordExists(
                        array(
                            'table'=>'chk_job_category',
                            'field'=>'name',
                            'adapter'=>$dbAdapter
                        )
        );

        if($validator->isValid($cat))
            $cat_exists=1;

        return $cat_exists;
    }

    public function jobCategorycheckAction(){
        $request=$this->getRequest();
        $jcat_exists=false;
        if($request->isPost())
            $jcat=$request->getPost('jcat');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_job_category',
                        'field'=>'name',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($jcat))
            $jcat_exists=true;

        $result=new JsonModel(array('jcat'=>$jcat,'success'=>$jcat_exists));

        return $result;
    }
}