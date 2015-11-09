<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'service' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/service',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'service',
                    ),
                ),
            ),
            'about' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/about',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'about',
                    ),
                ),
            ),
            'contact' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/contact',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'contact',
                    ),
                ),
            ),
            'feedback' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/feedback',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'feedback',
                    ),
                ),
            ),
            'event-home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/event',
                    'defaults' => array(
                        'controller' => 'Application\Controller\EventHome',
                        'action' => 'index',
                    ),
                ),
            ),
            'eventdetail' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/eventdetail[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\EventHome',
                        'action' => 'eventdetail',
                    ),
                ),
            ),
            'topemployer' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/topemployer[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'topEmployer',
                    ),
                ),
            ),
            'topuniversity' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/topuniversity[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'topuniversity',
                    ),
                ),
            ),
            
            'eventregistration' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/eventregistration[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\EventHome',
                        'action' => 'eventregistration',
                    ),
                ),
            ),
            'eventRegistrationCheck' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/eventRegistrationCheck',
                    'defaults' => array(
                        'controller' => 'Application\Controller\EventHome',
                        'action' => 'eventRegistrationCheck',
                    ),
                ),
            ),
            'eventarchives' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/eventarchives',
                    'defaults' => array(
                        'controller' => 'Application\Controller\EventHome',
                        'action' => 'eventarchives',
                    ),
                ),
            ),
            'jobDetails' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/jobdetails',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jobDetails',
                    ),
                ),
            ),
            'categoryDetails' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/categoryDetails[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'categoryDetails',
                    ),
                ),
            ),
            'jobs' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/jobs',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jobs',
                    ),
                ),
            ),
            'jobByRoles' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/roles',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jobByRoles',
                    ),
                ),
            ),
            'jobByCompanies' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/companies',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jobByCompanies',
                    ),
                ),
            ),
            'advancedJobSearch' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/advancedsearch',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'advancedJobSearch',
                    ),
                ),
            ),
            'cvView' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cvView',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'cvView',
                    ),
                ),
            ),
            'articles' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/articles[/:cid]',
                    'constraints' => array('cid' => '[0-9]+'),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Article',
                        'action' => 'index'
                    )
                )
            ),
            'articleDetails' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/articleDetails/[:id]',
                    'constraints' => array('id' => '[0-9]+'),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Article',
                        'action' => 'articleDetails'
                    )
                )
            ),
            'page' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/page[/:alias]',
                    'constraints' => array(
                        'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'page',
                    ),
                ),
            ),
            'spotlight' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/spotlight[/:alias]',
                    'constraints' => array(
                        'alias' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'spotlight',
                    ),
                ),
            ),
            'reportAbuse' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/application/reportAbuse',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'abuse',
                    ),
                ),
            )
        )
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory', // <-- add this
            'seeker_navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory', // <-- add this
        ),
    ),
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'module' => 'admin',
                'controller' => 'index',
                'uri' => '/admin',
                'class' => 'fa fa-dashboard'
            ),
            array(
                'label' => 'Manage Data',
                'route' => 'none',
                'class' => 'fa fa-database',
                'pages' => array(
                    array(
                        'label' => 'Buisness Type',
                        'id' => 'btype',
                        'uri' => '/admin/btype',
                        'route' => 'buisnesstype',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\BusinessType',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Membership Type',
                        'id' => 'mtype',
                        'uri' => '/admin/mtype',
                        'route' => 'membershipType',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\MembershipType',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Institute/University',
                        'id' => 'institute',
                        'uri' => '/admin/institute',
                        'route' => 'institute',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Institute',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Education Degree',
                        'id' => 'edgree',
                        'uri' => '/admin/edegree',
                        'route' => 'educationDegree',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\EducationDegree',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Department Type',
                        'id' => 'department',
                        'uri' => '/admin/department',
                        'route' => 'department',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Department',
                        'action' => 'index',
                        'use_route_match' => true,
                    ),
                    array(
                        'label' => 'Job Role',
                        'id' => 'department',
                        'uri' => '/admin/department/jobrole',
                        'route' => 'department',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Department',
                        'action' => 'jobrole'
                    ),
                    array(
                        'label' => 'Events',
                        'id' => 'event',
                        'uri' => '/admin/event',
                        'route' => 'events',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Event',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Event Registration',
                        'id' => 'evtreg',
                        'uri' => '/admin/evtreg',
                        'route' => 'evtreg',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Event',
                        'action' => 'evtreg'
                    ),
                    array(
                        'label' => 'Feedback',
                        'id' => 'feed',
                        'uri' => '/admin/feed',
                        'route' => 'feed',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Feed',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Article Category',
                        'id' => 'articleCategory',
                        'uri' => '/admin/articleCategory',
                        'route' => 'articleCategory',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\ArticleCategory',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Featured Articles',
                        'id' => 'article',
                        'uri' => '/admin/article',
                        'route' => 'article',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Article',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'CMS',
                        'id' => 'cms',
                        'uri' => '/admin/cms',
                        'route' => 'cms',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Cms',
                        'action' => 'index'
                    )
                ),
            ), // end of manage data
            array(
                'label' => 'Members',
                'route' => 'none',
                'class' => 'fa fa-users',
                'pages' => array(
                    array(
                        'label' => 'Admin User List',
                        'id' => 'admin_list',
                        'uri' => '/admin/adminuser',
                        'route' => 'admin',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'adminuser'
                    ),
                    array(
                        'label' => 'Seeker List',
                        'id' => 'seeker_list',
                        'uri' => '/admin/seeker',
                        'route' => 'admin',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'seeker'
                    ),
                    array(
                        'label' => 'Employer List',
                        'id' => 'employer_list',
                        'uri' => '/admin/company',
                        'route' => 'company',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Company',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Registered Member',
                        'id' => 'employer_list',
                        'uri' => '/admin/users',
                        'route' => 'admin',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'users'
                    ),
                    array(
                        'label' => 'Top Employer',
                        'id' => 'top_employer',
                        'uri' => '/admin/topemployer',
                        'route' => 'admin',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Index',
                        'action' => 'topemployer'
                    )
                )
            ), //end of Members
            array(
                'label' => 'Jobs',
                'route' => 'none',
                'class' => 'fa fa-laptop',
                'pages' => array(
                    array(
                        'label' => 'Functional Category',
                        'id' => 'job_category',
                        'uri' => '/admin/jcategory',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\JobCategory',
                        'action' => 'index',
                        'route' => 'jobCategory'
                    ),
                    array(
                        'label' => 'Industrial Category',
                        'id' => 'Job_industry',
                        'uri' => '/admin/Jobindustry',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\JobIndustry',
                        'action' => 'index',
                        'route' => 'JobIndustry'
                    ),
                    array(
                        'label' => 'Job Circular Type',
                        'id' => 'circular_Type',
                        'uri' => '/admin/circularType',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\JobCircularType',
                        'action' => 'index',
                        'route' => 'JobCircularType'
                    ),
                    array(
                        'label' => 'Spotlight Jobs',
                        'id' => 'spotlight_job',
                        'uri' => '/admin/job/spotlightjob',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Job',
                        'action' => 'spotlightjob',
                        'route' => 'adminjob'
                    ),
                    array(
                        'label' => 'Government Jobs',
                        'id' => 'govt_job',
                        'uri' => '/admin/job/govtjob',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Job',
                        'action' => 'govtjob',
                        'route' => 'adminjob'
                    ),
                    array(
                        'label' => 'Newspaper Jobs ',
                        'id' => 'newspaper_job',
                        'uri' => '/admin/job/newsjob',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Job',
                        'action' => 'newsjob',
                        'route' => 'adminjob'
                    ),
                    array(
                        'label' => 'Active Jobs ',
                        'id' => 'active_job',
                        'uri' => '/admin/job',
                        'module' => 'admin',
                        'action' => 'index',
                        'controller' => 'Admin\Controller\Job',
                        'route' => 'adminjob'
                    ),
                    array(
                        'label' => 'Archive Jobs ',
                        'id' => 'archive_job',
                        'uri' => '/admin/job/archive',
                        'module' => 'admin',
                        'action' => 'archive',
                        'controller' => 'Admin\Controller\Job',
                        'route' => 'adminjob'
                    ),
                    array(
                        'label' => 'Walk in Jobs',
                        'id' => 'archive_job',
                        'uri' => '/admin/job/walkin',
                        'module' => 'admin',
                        'action' => 'walkin',
                        'controller' => 'Admin\Controller\Job',
                        'route' => 'adminjob'
                    ),
                ),
            ), //end of Members
            array(
                'label' => 'Reports',
                'route' => 'none',
                'class' => 'fa fa-paste',
                'pages' => array(
                    array(
                        'label' => 'Spotlight Jobs',
                        'id' => 'spotlight_job',
                        'uri' => '/admin/reports',
                        'module' => 'admin',
                        'action' => 'spotlightJob',
                        'controller' => 'Admin\Controller\Reports',
                        'route' => 'Reports'
                    ),
                    array(
                        'label' => 'Government Jobs',
                        'id' => 'govt_job',
                        'uri' => '/admin/reports/govtJob',
                        'module' => 'admin',
                        'action' => 'govtJob',
                        'controller' => 'Admin\Controller\Reports',
                        'route' => 'Reports'
                    ),
                    array(
                        'label' => 'Newspaper Jobs',
                        'id' => 'newspaper_job',
                        'uri' => '/admin/reports/newsJob',
                        'module' => 'admin',
                        'action' => 'newsJob',
                        'controller' => 'Admin\Controller\Reports',
                        'route' => 'Reports'
                    ),
                    array(
                        'label' => 'Active Jobs ',
                        'id' => 'active_job',
                        'uri' => '/admin/reports/activeJob',
                        'module' => 'admin',
                        'action' => 'activeJob',
                        'controller' => 'Admin\Controller\Reports',
                        'route' => 'Reports'
                    ),
                    array(
                        'label' => 'Active Members ',
                        'id' => 'active_members',
                        'uri' => '/admin/reports/activeMembers',
                        'module' => 'admin',
                        'action' => 'activeMembers',
                        'controller' => 'Admin\Controller\Reports',
                        'route' => 'Reports'
                    ),
                ),
            ), // Reports
            array(
                'label' => 'Others',
                'route' => 'none',
                'class' => 'fa fa-paste',
                'pages' => array(
                    array(
                        'label' => 'Contact Inquery',
                        'id' => 'contact_us',
                        'uri' => '/admin/contact',
                        'module' => 'admin',
                        'action' => 'index',
                        'controller' => 'Admin\Controller\Contact',
                        'route' => 'Contacts'
                    ),
                ),
            ), //Others
            array(
                'label' => 'Access Controll',
                'route' => 'none',
                'class' => 'fa fa-paste',
                'pages' => array(
                    array(
                        'label' => 'Permission',
                        'id' => 'permission',
                        'uri' => '/admin/role/permissions',
                        'module' => 'admin',
                        'action' => 'permissions',
                        'controller' => 'Admin\Controller\Role',
                        'route' => 'role'
                    ),
                    array(
                        'label' => 'Role',
                        'id' => 'role',
                        'uri' => '/admin/role',
                        'module' => 'admin',
                        'action' => 'index',
                        'controller' => 'Admin\Controller\Role',
                        'route' => 'role'
                    ),
                ),
            ),
        ),
    ),
    // Seeker navigation 
    'seeker_navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'module' => 'admin',
                'controller' => 'index',
                'uri' => '/seeker',
                'class' => 'fa fa-dashboard'
            ),
            array(
                'label' => 'Update CV',
                'module' => 'admin',
                'controller' => 'index',
                'uri' => '/seeker/buildCv',
                'class' => 'fa fa-dashboard'
            ),
            array(
                'label' => 'My CV status',
                'module' => 'admin',
                'controller' => 'index',
                'uri' => '/seeker/buildCv',
                'class' => 'fa fa-dashboard'
            ),
            array(
                'label' => 'Saved',
                'module' => 'admin',
                'controller' => 'index',
                'uri' => '/seeker/buildCv',
                'class' => 'fa fa-dashboard'
            ),
            array(
                'label' => 'Manage Data',
                'route' => 'none',
                'class' => 'fa fa-database',
                'pages' => array(
                    array(
                        'label' => 'Buisness Type',
                        'id' => 'btype',
                        'uri' => '/admin/btype',
                        'route' => 'buisnesstype',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\BusinessType',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Membership Type',
                        'id' => 'mtype',
                        'uri' => '/admin/mtype',
                        'route' => 'membershipType',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\MembershipType',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Institute/University',
                        'id' => 'institute',
                        'uri' => '/admin/institute',
                        'route' => 'institute',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Institute',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Education Degree',
                        'id' => 'edgree',
                        'uri' => '/admin/edegree',
                        'route' => 'educationDegree',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\EducationDegree',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Department Type',
                        'id' => 'department',
                        'uri' => '/admin/department',
                        'route' => 'department',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Department',
                        'action' => 'index'
                    ),
                    array(
                        'label' => 'Job Role',
                        'id' => 'department',
                        'uri' => '/admin/department/jobrole',
                        'route' => 'department',
                        'module' => 'admin',
                        'controller' => 'Admin\Controller\Department',
                        'action' => 'jobrole'
                    ),
                ),
            ), // end of manage data
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\EventHome' => 'Application\Controller\EventHomeController',
            'Application\Controller\Article' => 'Application\Controller\ArticleController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'email/activate' => __DIR__ . '/../view/layout/email/activate.phtml',
            'email/seeker' => __DIR__ . '/../view/layout/email/seeker.phtml',
            'email/changepassword' => __DIR__ . '/../view/layout/email/changepassword.phtml',
            'email/confirmpassword' => __DIR__ . '/../view/layout/email/confirmpassword.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    // Module layouts configure 
    'module_layouts' => array(
        'Admin' => 'layout/admin'
    ),
);
