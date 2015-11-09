<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $this->layout('layout/seeker');
        return new ViewModel();
    }

    public function dashboardAction() {
        /* if (! $this->getServiceLocator()
          ->get('AuthService')->hasIdentity()){
          return $this->redirect()->toUrl('login');
          }
          $users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
          // $this->layout('layout/seeker');
          return new ViewModel(array ('user'=>$users));
         */
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        

        $total_job_count = \Admin\Model\Job::
                            where('status', '=', 1)
                            ->where('job_deadline', '>=', date("Y-m-d"))
                            ->where('posting_date', '<=', date("Y-m-d")) 
                            ->count();
     
       // $total_seeker_count = \Chkuser\Model\User::where('user_type', '=', 3 )->where('user_status', '=', 'active')->count();
       $total_seeker_count = \Chkuser\Model\User::where('user_type', '=', 3 )->count();
        
        //$total_employer_count = \Chkuser\Model\User::where('user_type', '=', 4 )->where('user_status', '=', 'active')->count();
        $total_employer_count = \Chkuser\Model\User::where('user_type', '=', 4 )->count();
        //$total_admin_count = \Chkuser\Model\User::where('user_type', '=', 2 )->where('user_status', '=', 'active')->count();
        $total_admin_count = \Chkuser\Model\User::where('user_type', '=', 2 )->count();
        
        $total_user_count = $total_seeker_count + $total_employer_count +$total_admin_count;   
        
        $total_contact_inquiry = \Admin\Model\Contact::where('view_status','=', 0)->count();

        $total_abuse_reports = \Admin\Model\ReportAbuse::where('status','=', 1)->count();
        
        $admin_last_login = $EloquentDb::table('chk_users')
                ->select('*')
                ->where('user_type', 2)
                ->where('trashed', 0)
                ->orderBy('updated_at', 'DESC')
                //->take(10);
                ->get();
        
         //print_r ($admin_last_login);
           //         exit ();
        return new ViewModel(array(
                    'total_job_count'=> $total_job_count,
                    'total_seeker_count' => $total_seeker_count,
                    'total_employer_count' => $total_employer_count,
                    'total_user_count' => $total_user_count,
                    'total_contact_inquiry' => $total_contact_inquiry,
                    'admin_last_login' => $admin_last_login,
                    'total_abuse_reports' => $total_abuse_reports
                ));
    }

    public function seekerAction() {
        
        $total_seeker_count = \Chkuser\Model\User::where('user_type', '=', 3 )->count();
        return new ViewModel(array('total_seeker' => $total_seeker_count));
    }

    public function adminUserAction() {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $results = $EloquentDb::table('chk_users')
                ->select('*')
                ->where('user_type', 2)
                ->where('trashed', 0)
                ->orderBy('full_name', 'ASC')
                //->take(100)
                ->get();
        
        
        return new ViewModel(array('user' => $results));
    }

    public function addAdminAction(){
        $is_validation_error=0;
        $error_messages=array();
        $request=$this->getRequest();
        $post_data=$request->getPost();
        $user=new \Chkuser\Model\User();

        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');

        $chk_roles=$EloquentDb::table('chk_roles')
                ->select('id','role_name')
                ->orderBy('role_name','ASC')
                ->get();

        if($request->isPost()){
            if($user::where('user_name','=',$request->getPost('user_name'))->first()){
                $error_messages[]="Username Already Exists";
                $is_validation_error=1;
            }
            if($user::where('email','=',$request->getPost('email'))->first()){
                $error_messages[]="Email Already Exists";
                $is_validation_error=1;
            }
            if($is_validation_error==0){
                $user->full_name=$request->getPost('full_name');
                $user->user_name=$request->getPost('user_name');
                $user->email=$request->getPost('email');
                $user->mobile_no=$request->getPost('mobile_no');
                $user->password=md5($request->getPost('password'));
                $user->user_role=$request->getPost('user_role');
                $user->user_type=2;
                $user->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('User has been added successfully.');
                return $this->redirect()->toUrl("/admin/adminuser");
            }
        }

        return new ViewModel(array(
            'error_messages' => $error_messages,
            'post_data' => $post_data,
            'chk_roles' => $chk_roles
        ));
    }

    public function isUsernameExists($username = "") {

        $user_exists = false;
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $validator = new \Zend\Validator\Db\RecordExists(
                array(
            'table' => 'chk_users',
            'field' => 'user_name',
            'adapter' => $dbAdapter
                )
        );

        if ($validator->isValid($username)) {

            $user_exists = 1;
        }

        return $user_exists;
    }

    public function isEmailExists($email = '') {

        $email_exists = false;
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $validator = new \Zend\Validator\Db\RecordExists(
                array(
            'table' => 'chk_users',
            'field' => 'email',
            'adapter' => $dbAdapter
                )
        );


        if ($validator->isValid($email)) {

            $email_exists = 1;
        }

        return $email_exists;
    }

    public function editAdminAction() {

        $is_validation_error = 0;
        $post_data = array();
        $error_messages = array();

        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $user = new \Chkuser\Model\User();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $chk_roles = $EloquentDb::table('chk_roles')
                ->select('id','role_name')
                ->orderBy('role_name', 'ASC')
                ->get();

        if ($request->isPost()) {

            $post_data = $request->getPost();

            if ($this->isUsernameValid($request->getPost('user_name'), $id)) {


                $error_messages[] = "Username Already Exists";
                $is_validation_error = 1;

            }

            if ($this->isEmailValid($request->getPost('email'), $id)) {
                 $error_messages[] = "Email Already Exists";

                $is_validation_error = 1;
            }

            if ($is_validation_error == 0) {

                $userVal = $user->find($id);

                $userVal->full_name = $request->getPost('full_name');
                $userVal->user_name = $request->getPost('user_name');
                $userVal->email = $request->getPost('email');
                $userVal->mobile_no = $request->getPost('mobile_no');

                if ($request->getPost('password') != "") {
                    $userVal->password = md5($request->getPost('password'));
                }

                
                $userVal->user_status  = $request->getPost('user_status');
                $userVal->user_role    = $request->getPost('user_role');
                

                $userVal->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                return $this->redirect()->toUrl("/admin/adminuser");
            }
        } else {

            $results = $user->find($id);
        }

        return new ViewModel(array(
            'error_messages' => $error_messages,
            'post_data' => $post_data,
            'results' => $results,
            'chk_roles' => $chk_roles
        ));
    }

    public function deleteAdminAction() {

        $id = $this->params()->fromRoute('id');
        $user = new \Chkuser\Model\User();

        $userVal = $user->find($id);
        $userVal->trashed = 1;
        $userVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('User has been deleted.');
        return $this->redirect()->toUrl("/admin/adminuser");
    }

    public function isUsernameValid($username = "", $id = "") {

        $user_exists = false;
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $validator = new \Zend\Validator\Db\RecordExists(
                array(
            'table' => 'chk_users',
            'field' => 'user_name',
            'adapter' => $dbAdapter,
            'exclude' => array(
                'field' => 'id',
                'value' => $id
            )
                )
        );


        if ($validator->isValid($username)) {
            $user_exists = 1;
        }

        return $user_exists;
    }

    public function isEmailValid($email = "", $id = "") {

        $email_exists = false;
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $validator = new \Zend\Validator\Db\RecordExists(
                array(
                    'table' => 'chk_users',
                    'field' => 'email',
                    'adapter' => $dbAdapter,
                    'exclude' => array(
                        'field' => 'id',
                        'value' => $id
                    )
                )
        );

        if ($validator->isValid($email)) {
            $email_exists = 1;
        }

        return $email_exists;
    }
       
    public function employerDeleteAction(){
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $user    = new \Chkuser\Model\User();

        $userVal = $user->find($id);
        $userVal->trashed = 1;
        $userVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Employer has been deleted.');
        return $this->redirect()->toUrl("/admin/employer");
    }
    
    public function settingsAction(){
        
        $is_validation_error = 0;
        $post_data = array();
        $error_messages = array();
        $success_messages = array();
        $request = $this->getRequest();
        $user = new \Chkuser\Model\User();
        
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
        
        //Session login info
        $loginusers = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                
        $login_user =  $EloquentDb::table('chk_users')
        ->select('user_name')
        ->where('id',  $loginusers->id)      
        ->get(); 
                
        if ($request->isPost()) {
            
            $post_data = $request->getPost();
            
            $settingsForm = new \Admin\Form\SettingsForm();
            $settingsForm->inputFilter->setData($request->getPost()->toArray());
            
            if ($this->isUsernameValid($request->getPost('user_name'), $loginusers->id)) {
                $error_messages[] = "User name Exists";
                $is_validation_error = 1;
            }
            
            if ($this->isAdminPasswordValid(md5($request->getPost('old_password')))) {
                $error_messages[] = "Old password does not match";
                $is_validation_error = 1;
            }            
            
            if (($is_validation_error == 0) && $settingsForm->inputFilter->isValid()) {
                
                $userVal = $user->find($loginusers->id);

                $userVal->user_name = $request->getPost('user_name');
                $userVal->password  = md5($request->getPost('new_password'));
                $userVal->save();
                
                return $this->redirect()->toUrl("/logout");
                
            }else {
                
                $messages = $this->getErrorMessages($settingsForm->inputFilter);  
                if (count($messages) > 0) {
                    $error_messages = $messages;
                }
            }
        }
                        
        return new ViewModel(array(
            'post_data'      => $post_data,
            'login_user'     => $login_user,
            'error_messages' => $error_messages            
        ));
        
    }
    
    public function getErrorMessages($formInpuFilter) {
        $error_messages = array();

        foreach ($formInpuFilter->getInvalidInput() as $messageId => $error) {
            $err = $error->getMessages();

            foreach ($err as $er) {
                $error_messages[] = $messageId . " : " . $er;
            }
        }

        return $error_messages;
    }
    
    public function isAdminPasswordValid($password = '') {

        $password_exists = false;
        $sm              = $this->getServiceLocator();
        $dbAdapter       = $sm->get('Zend\Db\Adapter\Adapter');
                
        $validator = new \Zend\Validator\Db\RecordExists(
                array(
                'table' => 'chk_users',
                'field' => 'password',
                'adapter' => $dbAdapter
                )
        );


        if (!$validator->isValid($password)) {

            $password_exists = 1;
        }

        return $password_exists;
    }
    
    public function usersAction(){
        
        $total_seeker_count = \Chkuser\Model\User::where('user_type', '=', 3 )->count();         
        $total_employer_count = \Chkuser\Model\User::where('user_type', '=', 4 )->count();
        $total_admin_count = \Chkuser\Model\User::where('user_type', '=', 2 )->count();
        
        $total_user_count = $total_seeker_count + $total_employer_count +$total_admin_count;
        
        return new ViewModel(array('total_user' => $total_user_count));
    }
    
    public function seekerEditAction() {
        
        $is_validation_error = 0;
        $post_data           = array();
        $error_messages      = array();
        
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $user    = new \Chkuser\Model\User();
        
        if ($request->isPost()) {
            
            $post_data = $request->getPost();
            
            if ($this->isEmailValid($request->getPost('email'), $id)) {
                
                $error_messages[] = "Email Already Exists";
                $is_validation_error = 1;                
            }
            
            if ($is_validation_error == 0) { 
            
                $userVal = $user->find($id);

                //$userVal->email       = $request->getPost('email');
                $userVal->user_status = $request->getPost('user_status');

                $userVal->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                return $this->redirect()->toUrl("/admin/seeker");
            }    
            
        } else {
            
            $results = $user->find($id);
        }

        return new ViewModel(array(            
            'error_messages' => $error_messages,            
            'post_data'      => $post_data,
            'results'        => $results
        ));
    }
    
    public function seekerDeleteAction() {
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $user    = new \Chkuser\Model\User();

        $userVal = $user->find($id);
        $userVal->trashed = 1;
        $userVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Seeker has been deleted.');
        return $this->redirect()->toUrl("/admin/seeker");
    }
            

    public function topEmployerAction(){
        
        return new ViewModel();
    }    

    public function emailcheckAction(){
        $request=$this->getRequest();
        $email_exists=false;
        if($request->isPost())
            $email=$request->getPost('email');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_users',
                        'field'=>'email',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($email))
            $email_exists=true;

        $result=new JsonModel(array('email'=>$email,'success'=>$email_exists));

        return $result;
    }
    
    public function notallowedAction()
    {
        return new ViewModel();
    }
    
}