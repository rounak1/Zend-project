<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Chkuser\Controller\Chkuser' => 'Chkuser\Controller\ChkuserController',
            'Chkuser\Controller\Login' => 'Chkuser\Controller\LoginController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'chkuser' => __DIR__ . '/../view',
        ),
    ),
    'service_manager' => array(
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'AuthService',
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            /* 'registration' => array(
              'type' => 'segment',
              'options' => array(
              'route' => '/registration[/:action][/:id]',
              'constraints' => array(
              'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
              'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
              ),
              'defaults' => array(
              'controller' => 'Chkuser\Controller\Chkuser',
              'action' => 'registration',
              ),
              ),
              ), */
            /*  'employer-registration' => array(
              'type' => 'Literal',
              'options' => array(
              'route' => '/employer-registration',
              'defaults' => array(
              'controller' => 'Chkuser\Controller\Chkuser',
              'action' => 'employer',
              ),
              ),
              ), */
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'Chkuser\Controller\Login',
                        'action' => 'index',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'Chkuser\Controller\Login',
                        'action' => 'logout',
                    ),
                ),
            ),
            'forgetPassword' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/forgetpassword',
                    'defaults' => array(
                        'controller' => 'Chkuser\Controller\Login',
                        'action' => 'forget',
                    ),
                ),
            ),
            'changepassword' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/changepassword[/:id]',
                    'defaults' => array(
                        'controller' => 'Chkuser\Controller\Login',
                        'action' => 'changepassword',
                    ),
                    'constraints' => array(
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                ),
            ),
        ),
    ),
);
