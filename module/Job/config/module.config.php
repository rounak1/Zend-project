<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Cjob\Controller\Index' => 'Job\Controller\IndexController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'job' => __DIR__ . '/../view',
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            'jobshow' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/job[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cjob\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            
            
        ),
    ),
);
