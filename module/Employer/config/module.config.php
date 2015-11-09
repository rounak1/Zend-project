<?php
return array(
    
    'controllers' => array(
        'invokables' => array(
            'Employer\Controller\Registration' => 'Employer\Controller\RegistrationController',
            'Employer\Controller\Index'        => 'Employer\Controller\IndexController',
            'Employer\Controller\Cv'           => 'Employer\Controller\CvController',
             'Employer\Controller\CvBank'      => 'Employer\Controller\CvBankController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'employer' => __DIR__ . '/../view',
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            'emp' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/emp[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Employer\Controller\Registration',
                        'action' => 'add',
                    ),
                ),
            ),
            
             'employer-registration' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/employer-registration',
                    'defaults' => array(
                        'controller' => 'Employer\Controller\Registration',
                        'action' => 'add',
                    ),
                ),
            ),
            
            'employer' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/employer[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Employer\Controller\Index',
                        'action' => 'dashboard',
                    ),
                ),
            ),
                                    
            'employer_cv' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/employer/cv[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Employer\Controller\Cv',
                        'action' => 'index',
                    ),
                ),
            ),
            'CvBank' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/employer/cvBank[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Employer\Controller\CvBank',
                        'action' => 'index',
                    ),
                ),
            ),
            
        ),
    ),
);