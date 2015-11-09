<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Admin\Controller\Index'           => 'Admin\Controller\IndexController',
            'Admin\Controller\BusinessType'    => 'Admin\Controller\BusinessTypeController',
            'Admin\Controller\Institute'       => 'Admin\Controller\InstituteController',
            'Admin\Controller\EducationDegree' => 'Admin\Controller\EducationDegreeController',
            'Admin\Controller\MembershipType'  => 'Admin\Controller\MembershipTypeController',
            'Admin\Controller\Department'      => 'Admin\Controller\DepartmentController',
            'Admin\Controller\Job'             => 'Admin\Controller\JobController',
            'Admin\Controller\JobCategory'     => 'Admin\Controller\JobCategoryController',
            'Admin\Controller\JobIndustry'     => 'Admin\Controller\JobIndustryController',
            'Admin\Controller\JobCircularType' => 'Admin\Controller\JobCircularTypeController',
            'Admin\Controller\Reports'         => 'Admin\Controller\ReportsController',
            'Admin\Controller\Company'         => 'Admin\Controller\CompanyController',
            'Admin\Controller\Contact'         => 'Admin\Controller\ContactController',
            'Admin\Controller\Event'           => 'Admin\Controller\EventController',
            'Admin\Controller\ArticleCategory' => 'Admin\Controller\ArticleCategoryController',
            'Admin\Controller\Article'         => 'Admin\Controller\ArticleController',
            'Admin\Controller\Cms'             => 'Admin\Controller\CmsController',
            'Admin\Controller\Role'            => 'Admin\Controller\RoleController',
            'Admin\Controller\Feed'            => 'Admin\Controller\FeedController',
            'Admin\Controller\ReportAbuse'            => 'Admin\Controller\ReportAbuseController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'admin' => __DIR__ . '/../view',
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            
            
            
            'admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'dashboard',
                    ),
                ),
            ),
            
            'dashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin',
                    
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'dashboard',
                    ),
                ),
            ),
            
            'company' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/company[/:action][/:cid][/:jid]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'cid' => '[0-9]+',
                        'jid' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Company',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'buisnesstype' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/btype[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\BusinessType',
                        'action' => 'index',
                    ),
                ),
            ),

            
            'institute' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/institute[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Institute',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'educationDegree' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/edegree[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\EducationDegree',
                        'action' => 'index',
                    ),
                ),
            ),
           
           
            'membershipType' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/mtype[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\MembershipType',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'department' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/department[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Department',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'adminjob' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/job[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Job',
                        'action' => 'index',
                    ),
                ),
            ),          
                      
            'jobCategory' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/jcategory[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\JobCategory',
                        'action' => 'index',
                    ),
                ),
            ),
            
            'JobIndustry' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/Jobindustry[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\JobIndustry',
                        'action' => 'index',
                    ),
                ),
            ),            
            'JobCircularType' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/circularType[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\JobCircularType',
                        'action' => 'index',
                    ),
                ),
            ),
            'Reports' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/reports[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Reports',
                        'action' => 'spotlightJob',
                    ),
                ),
            ),
            'Contacts' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/contact[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Contact',
                        'action' => 'index',
                    ),
                ),
            ),
            'articleCategory'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/articleCategory[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[a-zA-Z0-9][a-zA-Z0-9_-]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\ArticleCategory',
                        'action'=>'index'
                    )
                )
            ),
            'article'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/article[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[a-zA-Z0-9][a-zA-Z0-9_-]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\Article',
                        'action'=>'index'
                    )
                )
            ),
            'events' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/admin/event[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Admin\Controller\Event',
                        'action' => 'index',
                    ),
                ),
            ),
            'evtreg'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/evtreg[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[a-zA-Z0-9][a-zA-Z0-9_-]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\Event',
                        'action'=>'evtreg'
                    )
                )
            ),
            'feed'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/feed[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\Feed',
                        'action'=>'index'
                    )
                )
            ),
            'cms'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/cms[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\Cms',
                        'action'=>'index'
                    )
                )
            ),
            'role'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/role[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[0-9]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\Role',
                        'action'=>'index'
                    )
                )
            ),
            'report-abuse'=>array(
                'type'=>'segment',
                'options'=>array(
                    'route'=>'/admin/reportabuse[/:action][/:id]',
                    'constraints'=>array(
                        'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'=>'[a-zA-Z0-9][a-zA-Z0-9_-]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'Admin\Controller\ReportAbuse',
                        'action'=>'index'
                    )
                )
            ),
        )
    )
);