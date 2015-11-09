<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Chkapi\Controller\User' => 'Chkapi\Controller\UserController',
            'Chkapi\Controller\Api' => 'Chkapi\Controller\ApiController',
            'Chkapi\Controller\Cvrest' => 'Chkapi\Controller\CvrestController',
        ),
    ),
    // The following section is new` and should be added to your file
    'router' => array(
        'routes' => array(
            'chkapiuser' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/chkapi/user[/:action][/:id]',
                    'constraints' => array( 
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Chkapi\Controller\User',
                        'action'=> 'Index'
                    ),
                ),
            ),
            'chkapi' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/chkapi/api[/:action][/:id]',
                    'constraints' => array(
                        
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Chkapi\Controller\Api',
                        'action'=> 'Index'
                    ),
                ),
            ),
            'chkcvapi' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/chkapi/cv[/:action][/:id]',
                    'constraints' => array(
                        
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Chkapi\Controller\Cvrest',
                        'action'=> 'Index'
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(//Add this config
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
