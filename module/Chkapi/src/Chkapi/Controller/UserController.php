<?php

namespace Chkapi\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
//use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Authentication\Result;
use Chkuser\Model\User;
use Seeker\Model\Seeker;
use DB;

class UserController extends AbstractRestfulController {

    public function indexAction() {
        //return new ViewModel();
        $result = new JsonModel(array(
            'some_parameter' => 'some value',
            'success' => true,
        ));

        return $result;
    }

    /**
     * 
     */
    public function emailcheckAction() {
        $request = $this->getRequest();
        $email_exists = false;
        // $email = 'thinker231.bijon@gmail.com'; 
        if ($request->isPost()) {
            $email = $request->getPost('email');
        }


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
            $email_exists = true;
        }
        $result = new JsonModel(array(
            'email' => $email,
            'success' => $email_exists,
        ));

        return $result;

        //return $email_exists;
    }

    /*
     * 
     */

    public function usernamecheckAction() {
        $request = $this->getRequest();
        $username_exists = false;
        //$username = 'thinker.bijon@gmail.com'; 
        if ($request->isPost()) {
            $username = $request->getPost('username');
        }

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
            $username_exists = true;
        }
        $result = new JsonModel(array(
            'username' => $username,
            'success' => $username_exists,
        ));

        return $result;
    }

    public function getList() {
# code...
    }

    public function get($id) {
# code...
    }

    public function create($data) {
# code...
    }

    public function update($id, $data) {
# code...
    }

    public function delete($id) {
# code...
    }

    public function loginAction() {

        $authservice = $this->getServiceLocator()->get('AuthService');
        $request = $this->getRequest();
        if ($request->isPost()) {

            $post_data = $request->getPost();
            $form = new \Chkuser\Form\LoginForm();
            $form->setInputFilter($form->inputFilter);
            //$form->setData($request->getPost());
            $form->setData($post_data);
            if ($form->isValid()) {
                //check authentication...
                $authservice->getAdapter()
                        ->setIdentity($post_data['user_name'])
                        ->setCredential($post_data['password']);

                $result = $authservice->authenticate();
                if ($result->isValid()) {

                    $identity = $result->getIdentity();
                    $resultObject = $authservice->getAdapter()->getResultRowObject();
                    // update user last login 
                    $user = User::find($resultObject->id);
                    $user->updated_at = date('Y-m-d h:i:s');
                    $user->save();

                    // get profile image 
                    $configs = $this->getServiceLocator()->get('Config');


                    /* $resultObject->last_login = $user->updated_at;
                      $resultObject->profile_image = $profile_image;
                      $resultObject->profile_thumbnail_image = $profile_thumbnail_image;
                     */
                    $storage = $authservice->getStorage();
                    $storage->write(
                            $resultObject
                    );

                    if ($user->user_type == $configs['config_user_type']['seeker']) {

                        $seeker = Seeker::where('user_id', '=', $resultObject->id)->get()->first();
                        $profile_image = $seeker->profile_picture;
                        $profile_thumbnail_image = $this->getDiffImage($profile_picture, 'thumbnail');
                        $destination_image_path = '/upload/cv/';
                        $exists_profile_picture = 1;
                    } else if ($user->user_type == $configs['config_user_type']['employer']) {
                        
                    }else if ($user->user_type == $configs['config_user_type']['admin']) {
                        
                    }
                    
                    else {
                        $profile_image = '';
                        $profile_thumbnail_image = '';
                    }

                    $resultObject->last_login = $user->updated_at;
                    $resultObject->profile_image = $destination_image_path.$profile_image;
                    $resultObject->profile_thumbnail_image = $destination_image_path.$profile_thumbnail_image;


                    $EloquentDb = $this->getServiceLocator()
                            ->get('EloquentZF2Adapter');



                    $success_error = 0;

                    if ($post_data['type'] == "save") {
                        $apply_for = "save";
                        $status = $this->getAlreadySave($resultObject->id, $post_data['job_id']);
                    } else {
                        $apply_for = "apply";
                        $status = $this->getAlreadyApply($resultObject->id, $post_data['job_id']);

                        $chk_cv = $EloquentDb::table('chk_cv')
                                ->select('id', 'resume_title')
                                ->where('user_id', $resultObject->id)
                                ->get();


                        $apply_form .= '<form id="applyJobForm" method="post" class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-xs-3 control-label">Expected salary</label>
                                                <div class="col-xs-5">
                                                    <input type="text" class="form-control" name="expected_salary" required />
                                                </div>
                                            </div>';

                        $apply_form .= '<div class="form-group">
                                            <label class="col-xs-3 control-label">Select your cv</label>
                                            <div class="col-xs-5">
                                                <select name="cv_id" required>
                                                    <option value="">Select cv</option>';
                        foreach ($chk_cv as $val) {
                            $apply_form .='<option value="' . $val['id'] . '">' . $val['resume_title'] . '</option>';
                        }

                        $apply_form .='      </select>
                                            </div>
                                        </div>';

                        $apply_form .= '    <div class="form-group">
                                                <div class="col-xs-5 col-xs-offset-3">
                                                    <button type="submit" class="btn btn-default after_login_apply">Apply</button>
                                                    <button type="submit" class="btn btn-default back_button">Back</button>
                                                    <input type="hidden" value="' . $post_data['job_id'] . '" name="id"/>
                                                    <input id="job_type" type="hidden" value="apply" name="type" />
                                                </div>
                                            </div>
                                        </form>
                                        ';
                    }

                    $authservice->setStorage($this->getSessionStorage());
                    //$redirect_url = $this->getAuthRedirectUrl($resultObject->user_type);
                    //return $this->redirect()->toUrl($redirect_url);
                } else {

                    switch ($result->getCode()) {

                        case Result::FAILURE_IDENTITY_NOT_FOUND:
                            $error_messages[] = 'username/password is not found';
                            break;
                        case Result::FAILURE_CREDENTIAL_INVALID:
                            $error_messages[] = 'Username/ password is not valid.Or you have to activate your Regisration';
                            break;
                        case Result::SUCCESS:
                            break;

                        default:
                            $error_messages[] = 'Sorry ! Login Failed.';
                            break;
                    }
                }
            } else {
                # code...
                foreach ($form->inputFilter->getMessages() as $messageId => $error) {
                    foreach ($error as $msg) {

                        $error_messages[] = $msg;
                    }
                }
            }  //end isFormValid  
        }//end isPost

        $error_flag = 0;
        $success = 1;
        if (count($error_messages) > 0) {
            foreach ($error_messages as $msg) {
                $err_msgs .= $msg . '<br> ';
            }
            $error_flag = 1;
            $success = 0;
        }
        $job_id = $request->getPost('job_id');

        $job_url = '/job/show/' . $job_id;
        $result = new JsonModel(array(
            'success' => $success,
            'error_flag' => $error_flag,
            'redirect_url' => $job_url,
            'err_msgs' => $err_msgs,
            'apply_for' => $apply_for,
            'apply_form' => $apply_form,
            'status' => $status
        ));

        return $result;
    }

    public function getAlreadyApply($user_id, $job_id) {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $status = 'insert_apply';

        $check_job = $EloquentDb::table('chk_job_application')
                ->select('*')
                ->where('job_id', $job_id)
                ->where('user_id', $user_id)
                //->where('app_status', 'apply')
                ->first();

        if (count($check_job) > 0) {

            if ($check_job['app_status'] == 'save') {

                /*
                  $EloquentDb::table('chk_job_application')
                  ->where('job_id', $job_id)
                  ->where('user_id', $user_id)
                  ->update(array(
                  'app_status' => 'apply')
                  );
                 * 
                 */
                $status = 'update_apply';
            } else {
                $status = 'already_apply';
            }
        }

        return $status;
    }

    public function getAlreadySave($user_id, $job_id) {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $status = 'insert_apply';

        $check_job = $EloquentDb::table('chk_job_application')
                ->select('*')
                ->where('job_id', $job_id)
                ->where('user_id', $user_id)
                //->where('app_status', 'apply')
                ->first();

        if (count($check_job) > 0) {
            if ($check_job['app_status'] == 'apply') {
                $status = 'already_apply';
            } else {
                $status = 'already_save';
            }
        } else {
            $EloquentDb::table('chk_job_application')->insert(
                    [
                        'job_id' => $job_id,
                        'user_id' => $user_id,
                        'app_status' => "save"
                    ]
            );

            $status = 'insert_save';
        }

        return $status;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()
                    ->get('Chkuser\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function getDiffImage($filename, $size = "thumbnail") {

        $extension = $this->getExtension($filename);
        $name_prefix = substr($filename, 0, strrpos($new_file_name, ".$extenstion"));

        $image_size = $name_prefix . '_' . $size . '.' . $extension;
        //$medium_image = $name_prefix . '_medium.' . $extension;
        //$thumbnail_image = $name_prefix . '_thumbnail.' . $extension;

        return $image_size;
    }

    public function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }
}