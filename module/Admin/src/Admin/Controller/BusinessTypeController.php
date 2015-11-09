<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
//use Carbon\Carbon;
//use Admin\Model\BusinessType;

class BusinessTypeController extends AbstractActionController {

    public function indexAction() {        
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_business_type')
        ->select('chk_business_type.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_business_type.modified_by', '=', 'chk_users.id')
        ->where('chk_business_type.status','=', '1')
        ->orderBy('chk_business_type.name','ASC')
        ->get();
                       
        return new ViewModel(array('results' => $results));
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $btype = new \Admin\Model\BusinessType();

        if ($request->isPost()) {
//            if ($btype::where('name', '=', $request->getPost('bname'))->first()) {
//                $this->flashMessenger()->setNamespace('error')->addMessage('This Business Type Already Exist.');
//                return $this->redirect()->toUrl("/admin/btype");
//            }else{
                $businessType = $btype->find($id);            
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();            
                $businessType->name = $request->getPost('bname');
                $businessType->modified_by = $auth_users->id;
                $businessType->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('This Business Type Updated');
                return $this->redirect()->toUrl("/admin/btype");
//            }
        } else {
            $results = $btype->find($id);
        }

        return new ViewModel(array('btypes' => $results));
    }

    public function addAction() {
        $request = $this->getRequest();
        $post_data = $request->getPost();
        $btype = new \Admin\Model\BusinessType();
        $error_messages=array();

        if($request->isPost()){
            if($btype::where(array('name'=>$request->getPost('bname'),'status'=>1))->first()){
                $error_messages[]='This Business Type already exists.';
            }else{
                $btype->name = $request->getPost('bname');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $btype->created_by = $auth_users->id;
                $btype->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Business Type has been added.');
                return $this->redirect()->toUrl("/admin/btype");
            }
        }

        return new ViewModel(array(
                'post_data' => $post_data,
                'error_messages' => $error_messages
            )
        );
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $btype = new \Admin\Model\BusinessType();

        $businessType = $btype->find($id);
        $businessType->status = 0;
        $businessType->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('This Business Type has been deleted.');
        return $this->redirect()->toUrl("/admin/btype");
    }

    public function btypecheckAction(){
        $request=$this->getRequest();
        $btype_exists=false;
        if($request->isPost())
            $btype=$request->getPost('btype');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_business_type',
                        'field'=>'name',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($btype))
            $btype_exists=true;

        $result=new JsonModel(array('btype'=>$btype,'success'=>$btype_exists));

        return $result;
    }
}