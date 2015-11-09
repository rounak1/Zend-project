<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class DepartmentController extends AbstractActionController {

    public function indexAction() {
        //$department = new \Admin\Model\Department();
        //$results = $department::where('status','=','1')->get();

        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_department')
        ->select('chk_department.*', 'chk_users.full_name')
        ->leftJoin('chk_users', 'chk_department.modified_by', '=', 'chk_users.id')
        ->where('chk_department.status','=', '1')
        ->orderBy('chk_department.name','ASC')
        ->get();

        return new ViewModel(array('departments' => $results));
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $dtype = new \Admin\Model\Department();
        $results = $dtype->find($id);

        if ($request->isPost()) {
            $department = $dtype->find($id);
//            if ($dtype::where('name', '=', $request->getPost('dname'))->first()) {
//               $this->flashMessenger()->setNamespace('error')->addMessage('This Department type already exist.');
//                return $this->redirect()->toUrl("/admin/department");
//            }else{
                $department->name        = $request->getPost('dname');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $department->modified_by = $auth_users->id;
                $department->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Department type has been updated.');
                return $this->redirect()->toUrl("/admin/department");
//            }
        }

        return new ViewModel(array('dtypes' => $results));
    }

    public function addAction(){
        $request    = $this->getRequest();
        $post_data  = $request->getPost();
        $dtype      = new \Admin\Model\Department();
        $error_messages=array();

        if($request->isPost()){
            if($dtype::where(array('name'=>$request->getPost('dname'),'status'=>1))->first()){
                $error_messages[]="This Department type already exists.";
            }else{
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $dtype->created_by = $auth_users->id;
                $dtype->name       = $request->getPost('dname');
                $dtype->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Department type has been added.');
                return $this->redirect()->toUrl("/admin/department");
            }
        }

        return new ViewModel(array(
                'post_data'=>$post_data,
                'error_messages'=>$error_messages
            )
        );
    }

    public function deleteAction() {

        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $dtype = new \Admin\Model\Department();
        
        $department = $dtype->find($id);
        $department->status = 0;
        $department->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Department type has been deleted.');
        return $this->redirect()->toUrl("/admin/department");
    }

    public function jobroleAction(){
        //$role    = new \Admin\Model\PreferredRole();        
        //$results = $role::where('status','=','1')->get();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $results = $EloquentDb::table('chk_preferred_role')
           ->select('chk_preferred_role.*', 'chk_users.full_name', 'chk_department.name AS role_name')
           ->join('chk_users', 'chk_users.id', '=', 'chk_preferred_role.created_by')                           
           ->join('chk_department', 'chk_department.id', '=', 'chk_preferred_role.department_id')     
           ->where('chk_preferred_role.trashed', 0)
           ->orderBy('role_name','ASC')
           ->orderBy('name','ASC')
           ->get();
        
        return new ViewModel(array('role' => $results));
    }

    public function roleaddAction() {
        $request    = $this->getRequest();       
        $role      = new \Admin\Model\PreferredRole();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Depeartment list
        $functions = $EloquentDb::table('chk_department')
           ->select('id', 'name')
           ->where('status', 1)     
           ->where('trashed', 0)
           ->orderBy('name', 'ASC')
           ->get();        
       
        if ($request->isPost()) {
            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $role->created_by    = $auth_users->id;
            $role->name          = $request->getPost('rname');
            $role->department_id = $request->getPost('department_id');
            $role->status        = $request->getPost('status'); 
            $role->save();

            $this->flashMessenger()->setNamespace('success')->addMessage('Preferred role has been added.');
            return $this->redirect()->toUrl("/admin/department/jobrole");
        }

        return new ViewModel(array(
                                    'functions' => $functions
                            ));
    }

    public function editroleAction() {
        $request = $this->getRequest();       
        $role    = new \Admin\Model\PreferredRole();
        $id      = $this->params()->fromRoute('id');
        $results = $role->find($id);

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Depeartment list
        $functions = $EloquentDb::table('chk_department')
           ->select('id', 'name')
           ->where('status', 1)     
           ->where('trashed', 0)
           ->orderBy('name','asc')
           ->get();

        if ($request->isPost()) {
            $roleVal = $role->find($id);
            $roleVal->name          = $request->getPost('rname');
            $roleVal->department_id = $request->getPost('department_id');
            $roleVal->status        = $request->getPost('status');
            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $roleVal->modified_by   = $auth_users->id;
            $roleVal->save();

            $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
            return $this->redirect()->toUrl("/admin/department/jobrole");
        }

        return new ViewModel(array(
                'results' => $results,
                'functions' => $functions
            )
        );
    }

    public function deleteroleAction() {
        $id      = $this->params()->fromRoute('id');
        $role    = new \Admin\Model\PreferredRole();
        
        $roleVal = $role->find($id);
        $roleVal->trashed = 1;
        $roleVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Preferred role has been deleted.');
        return $this->redirect()->toUrl("/admin/department/jobrole");
    }

    public function dnamecheckAction(){
        $request=$this->getRequest();
        $dept_exists=false;
        if($request->isPost())
            $dept=$request->getPost('dname');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_department',
                        'field'=>'name',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($dept))
            $dept_exists=true;

        $result=new JsonModel(array('dept'=>$dept,'success'=>$dept_exists));

        return $result;
    }
}