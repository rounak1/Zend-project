<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
//use Admin\Model\BusinessType;

class MembershipTypeController extends AbstractActionController {

    public function indexAction() {
      $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

      $results =  $EloquentDb::table('chk_membership_service')
        ->select('chk_membership_service.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_membership_service.modified_by', '=', 'chk_users.id')
        ->where('status', 1)
        ->orderBy('name','ASC')
        ->get();

        return new ViewModel(array('mtypes' => $results));
    }

    public function addAction() {
        $request = $this->getRequest();
        $post_data = $request->getPost();
        $mtype = new \Admin\Model\MembershipType();
        $error_messages=array();

        if ($request->isPost()) {
            if ($mtype::where(array('name'=>$request->getPost('mtname'),'status'=>1))->first()) {
                $error_messages[]="This Membership Type already exists.";
            }else{
                $mtype->name = $request->getPost('mtname');
                $mtype->description = $request->getPost('description');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $mtype->created_by = $auth_users->id;
                $mtype->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Membership Type has been added.');
                return $this->redirect()->toUrl("/admin/mtype");
            }
        }

        return new ViewModel(array(
                'post_data' => $post_data,
                'error_messages'=>$error_messages
            )
        );
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $mtype = new \Admin\Model\MembershipType();

        if ($request->isPost()) {
//            if ($mtype::where('name', '=', $request->getPost('mtname'))->first()) {
//               $this->flashMessenger()->setNamespace('error')->addMessage('This Membership Type already exist.');
//                return $this->redirect()->toUrl("/admin/mtype");
//            }else{
                $membershipType = $mtype->find($id);
                $membershipType->name = $request->getPost('mtname');
                $membershipType->description = $request->getPost('description');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $membershipType->modified_by = $auth_users->id;
                $membershipType->save();
                $this->flashMessenger()->setNamespace('success')->addMessage('This Membership Type has been updated');
                return $this->redirect()->toUrl("/admin/mtype");
//            }
        } else {
            $results = $mtype->find($id);
        }

        return new ViewModel(array('mtypes' => $results));
    }

    public function deleteAction() {        

        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $mtype   = new \Admin\Model\MembershipType();

        $membershipType = $mtype->find($id);
        $membershipType->status = 0;
        $membershipType->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Membership Type has been deleted.');
        return $this->redirect()->toUrl("/admin/mtype");
    }

    public function mtypecheckAction(){
        $request=$this->getRequest();
        $mtype_exists=false;
        if($request->isPost())
            $mtype=$request->getPost('mName');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_membership_service',
                        'field'=>'name',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($mtype))
            $mtype_exists=true;

        $result=new JsonModel(array('mtype'=>$mtype,'success'=>$mtype_exists));

        return $result;
    }
}