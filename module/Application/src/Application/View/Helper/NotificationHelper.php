<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class NotificationHelper extends AbstractHelper {

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


            $data['total_notification'] = $EloquentDb::table('chk_notification')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->count();

            $data['all_notification'] = $EloquentDb::table('chk_notification')
                    ->where('to_user_id', $session_data->id)
                    ->where('to_user_status', 'unread')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();

            return $data;
        }
    }

}
