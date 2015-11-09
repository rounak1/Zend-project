<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class EducationDegreeController extends AbstractActionController{

    public function indexAction(){
        //$eDegree = new \Admin\Model\EducationDegree();
        //$results = $eDegree::where('status','=','1')->get();

        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_education_degree')
        ->select('chk_education_degree.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_education_degree.modified_by', '=', 'chk_users.id')
        ->where('chk_education_degree.status','=', '1')
        ->orderBy('chk_education_degree.name','ASC')
        ->get();

         //$this->layout('layout/admin');

        return new ViewModel(array ('eDegree'=>$results));
    }

    public function editAction() {
        $request  = $this->getRequest();
        $id       = $this->params()->fromRoute('id');
        $eDegree = new \Admin\Model\EducationDegree();
        
        if ($request->isPost()){
//            if ($eDegree::where('name', '=', $request->getPost('degree_name'))->first()) {
//                $this->flashMessenger()->setNamespace('error')->addMessage('This Degree name already exist.');
//                return $this->redirect()->toUrl("/admin/edegree");
//            }else{
                $degreeVal       = $eDegree->find($id);
                $degreeVal->name = $request->getPost('degree_name');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $degreeVal->modified_by = $auth_users->id;
                $degreeVal->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Degree type has been updated');
                return $this->redirect()->toUrl("/admin/edegree");
//            }
        } else {
            $results = $eDegree->find($id);
        }

        return new ViewModel(array ('edegree'=>$results));
    }

    public function addAction(){
        $request          = $this->getRequest();
        $post_data        = $request->getPost();
        $edegree          = new \Admin\Model\EducationDegree();
        $error_messages=array();

        if($request->isPost()){
            if($edegree::where(array('name'=>$request->getPost('dname'),'status'=>1))->first()){
                $error_messages[]="This Degree name already exist.";
            }else{
                $edegree->name = $request->getPost('dname');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $edegree->created_by = $auth_users->id;            
                $edegree->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Degree has been added.');
                return $this->redirect()->toUrl("/admin/edegree");
            }
        }

        return new ViewModel(array(
                'post_data' => $post_data,
                'error_messages'=>$error_messages
            )
        );
    }

    public function deleteAction() {        

        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $edegree = new \Admin\Model\EducationDegree();

        $degreeVal = $edegree->find($id);
        $degreeVal->status = 0;
        $degreeVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Degree has been deleted.');
        return $this->redirect()->toUrl("/admin/edegree");
    }

    public function edegreecheckAction(){
        $request=$this->getRequest();
        $edegree_exists=false;
        if($request->isPost())
            $edegree=$request->getPost('edegree');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_education_degree',
                        'field'=>'name',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($edegree))
            $edegree_exists=true;

        $result=new JsonModel(array('edegree'=>$edegree,'success'=>$edegree_exists));

        return $result;
    }
}