<?php

namespace Admin;

//use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach('route', array($this, 'AuthCheck'), 2);
    }

    public function AuthCheck(MvcEvent $e) {


        /* $access['Manage Data'] = array ('Admin\Controller\Index',
          'Admin\Controller\BusinessType',
          'Admin\Controller\MembershipType',
          'Admin\Controller\Institute',
          'Admin\Controller\EducationDegree',
          'Admin\Controller\Department',
          'Admin\Controller\Department',
          'Admin\Controller\Event',
          'Admin\Controller\Feed',
          'Admin\Controller\ArticleCategory',
          'Admin\Controller\Article',
          'Admin\Controller\Cms',
          ''

          ); */

        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        /* $EloquentDb = $e->getServiceLocator()
          ->get('EloquentZF2Adapter'); */
        $EloquentDb = $sm->get('EloquentZF2Adapter');

        //$role_controllers [''][] = '';


        $router = $sm->get('router');
        $request = $sm->get('request');


        $matchedRoute = $router->match($request);


        if (null !== $matchedRoute) {
            $controller = $matchedRoute->getParam('controller');
            $action = $matchedRoute->getParam('action');



            //  echo $controller; echo ' :action : '.$action;exit ();
            //Manage Data
            $permission = array();

            // permit all the menu
            $permission['Admin\Controller\Index']['dashboard'] = array('Manage Data', 'Members', 'Jobs', 'Reports', 'Others');


            $permission['Admin\Controller\Index']['index'] = array('Manage Data');
            $permission['Admin\Controller\BusinessType']['index'] = array('Manage Data');
            $permission['Admin\Controller\Institute']['index'] = array('Manage Data');
            $permission['Admin\Controller\MembershipType']['index'] = array('Manage Data');
            $permission['Admin\Controller\EducationDegree']['index'] = array('Manage Data');
            $permission['Admin\Controller\Department']['jobrole'] = array('Manage Data');
            $permission['Admin\Controller\Event']['index'] = array('Manage Data');
            $permission['Admin\Controller\Feed']['index'] = array('Manage Data');
            $permission['Admin\Controller\ArticleCategory']['index'] = array('Manage Data');
            $permission['Admin\Controller\Article']['index'] = array('Manage Data');
            $permission['Admin\Controller\Cms']['index'] = array('Manage Data');

            //
            $permission['Admin\Controller\Index']['adminuser'] = array('Members');
            $permission['Admin\Controller\Index']['seeker'] = array('Members');
            $permission['Admin\Controller\Company']['index'] = array('Members');

            //Admin\Controller\Index

            $permission['Admin\Controller\Index']['users'] = array('Members');
            $permission['Admin\Controller\Index']['topemployer'] = array('Members');


            // jobs
            $permission['Admin\Controller\JobCategory']['index'] = array('Jobs');
            $permission['Admin\Controller\JobIndustry']['index'] = array('Jobs');
            $permission['Admin\Controller\JobCircularType']['index'] = array('Jobs');
            $permission['Admin\Controller\Job']['spotlightjob'] = array('Jobs');
            $permission['Admin\Controller\Job']['govtjob'] = array('Jobs');
            $permission['Admin\Controller\Job']['newsjob'] = array('Jobs');
            $permission['Admin\Controller\Job']['index'] = array('Jobs');
            $permission['Admin\Controller\Job']['archive'] = array('Jobs');
            $permission['Admin\Controller\Job']['walkin'] = array('Jobs');

            //Reports

            $permission['Admin\Controller\Reports']['activeJob'] = array('Reports');
            $permission['Admin\Controller\Reports']['newsJob'] = array('Reports');
            $permission['Admin\Controller\Reports']['govtJob'] = array('Reports');
            $permission['Admin\Controller\Reports']['spotlightJob'] = array('Reports');

            //Admin\Controller\Role 

            $permission['Admin\Controller\Role']['index'] = array('Access Controll');
            $permission['Admin\Controller\Role']['permissions'] = array('Access Controll');

            // Others 

            $permission['Admin\Controller\Contact']['index'] = array('Others');
            $permission['Admin\Controller\Contact']['permissions'] = array('Others');



            $module_array = explode('\\', $controller);
            $module = $module_array[0];

            // check auth...
            $response = $e->getResponse();

            if ($module == 'Admin') {
                if (!$sm->get('AuthService')->hasIdentity()) {
                    $url = $router->assemble(array(), array('name' => 'login'));
                    $response->setStatusCode(302);
                    $response->getHeaders()->addHeaderLine('Location', $url);
                }
                $users = $sm->get('AuthService')->getStorage()->read();
                /* echo "<pre>";
                  print_r ($users);
                  exit (); */

                $configs = $sm->get('config');
                if ($users->user_type != $configs['config_user_type']['admin']) {
                    $url = $router->assemble(array(), array('name' => 'login'));
                    $response->setStatusCode(302);
                    $response->getHeaders()->addHeaderLine('Location', $url);
                }


                //$users = $sm->get('AuthService')->getStorage()->read();


                $chk_roles = $EloquentDb::table('chk_roles')
                        ->select('chk_roles.id', 'chk_roles.created_at', 'chk_roles.role_name', 'chk_permissions.perm_name')
                        ->leftJoin('chk_role_permissions', 'chk_role_permissions.role_id', '=', 'chk_roles.id')
                        ->leftJoin('chk_permissions', 'chk_permissions.id', '=', 'chk_role_permissions.perm_id')
                        ->where('chk_roles.role_name', '=', $users->user_role)
                        ->get();



                $perms_array = $permission[$controller][$action];


                $not_allowed = 0;
                foreach ($chk_roles as $value) {
                    //$perm_roles[] = $value['perm_name'];
                    //echo $value['role_name'];
                    //exit ();


                    if ($value['role_name'] == 'Admin') {
                        $not_allowed = 1;
                        break;
                    }
                    $perm_name = $value['perm_name'];
                    //echo $perm_name; exit ();

                    if (in_array($perm_name, $perms_array)) {
                        $not_allowed = 1;
                        break;
                    }
                }

                if ($action == 'notallowed') {
                    $not_allowed = 1;
                }

//var_dump ();

                $response = $e->getResponse();
                if ($not_allowed == 0) {

                    $url = '/admin/notallowed';
                    $response->setStatusCode(302);
                    $response->getHeaders()->addHeaderLine('Location', $url);
                }
            }
        }
    }

}
