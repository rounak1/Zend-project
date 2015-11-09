<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class SeekerHelper extends AbstractHelper {

    protected $params;
    protected $moduleName;
    protected $controllerName;
    protected $actionName;
    protected $routeName;

    public function __invoke() {
        $this->params = $this->initialize();
        return $this->params;
    }

    protected function initialize() {

        //echo "here"; 
        $params = array();
        $sm = $this->getView()->getHelperPluginManager()->getServiceLocator();

        $EloquentDb = $sm
                ->get('EloquentZF2Adapter');
        $auth = $sm->get('AuthService');
        if ($auth->hasIdentity()) {

            $session_data = $sm->get('AuthService')->getStorage()->read();
            $user_id = $session_data->id;

            $data = array();

            $data['seeker'] = $EloquentDb::table('chk_seeker_profile')
                    ->where('chk_seeker_profile.user_id', $session_data->id)
                    ->first();

            $data['seeker_message_notification'] = $EloquentDb::table('chk_users_message')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->count();

            $data['seeker_all_unread_message'] = $EloquentDb::table('chk_users_message')
                    ->select('*')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

            $data['seeker_total_notification'] = $EloquentDb::table('chk_notification')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->count();

            $data['seeker_all_notification'] = $EloquentDb::table('chk_notification')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

            /* $data['seeker_cv_progress'] = $EloquentDb::table('chk_cv')
              ->select('reference_progress +
              contact_progress +
              personal_progress +
              career_progress +
              targeted_progress +
              education_progress +
              experience_progress +
              training_progress
              ')
              ->where('user_id', '=', $session_data->id)
              ->where('type', '=', 'native')
              ->first();
             */

            $progress = $EloquentDb::table('chk_cv')
                    ->select('reference_progress', 'contact_progress', 'personal_progress', 'career_progress', 'targeted_progress', 'education_progress', 'experience_progress', 'training_progress'
                    )
                    ->where('user_id', '=', $session_data->id)
                    ->where('type', '=', 'native')
                    ->first();

            $seeker_cv_progress = $progress["reference_progress"] +
                    $progress["contact_progress"] +
                    $progress["personal_progress"] +
                    $progress["career_progress"] +
                    $progress["targeted_progress"] +
                    $progress["education_progress"] +
                    $progress["experience_progress"] +
                    $progress["training_progress"];


            if ($seeker_cv_progress > 100) {
                $data['seeker_cv_progress'] = 100;
            } else {
                $data['seeker_cv_progress'] = $seeker_cv_progress;
            }

            $chk_cv = $EloquentDb::table('chk_cv')
                    ->select('updated_at')
                    ->where('user_id', '=', $session_data->id)
                    ->where('type', '=', 'native')
                    ->first();

            $data['last_update_my_resume'] = $chk_cv["updated_at"];

            //Saved job
            $data['seeker_saved_job'] = $EloquentDb::table('chk_job_application')
                    ->where('user_id', $session_data->id)
                    ->where('app_status', 'save')
                    ->count();

            //Apply job
            $data['seeker_apply_job'] = $EloquentDb::table('chk_job_application')
                    ->where('user_id', $session_data->id)
                    ->where('app_status', 'apply')
                    ->count();

            return $data;
        }
    }

}
