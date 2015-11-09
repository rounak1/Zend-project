<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Seeker\Controller\Registration' => 'Seeker\Controller\RegistrationController',
            'Seeker\Controller\Index' => 'Seeker\Controller\IndexController',
            'Seeker\Controller\Cv' => 'Seeker\Controller\CvController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'seeker' => __DIR__ . '/../view',
        ),
    ),
    // Routes
    'router' => array(
        'routes' => array(
            'registration' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/registration[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Registration',
                        'action' => 'add',
                    ),
                ),
            ),
            'gmail' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/gmail',
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Registration',
                        'action' => 'callbackgmail',
                    ),
                ),
            ),
            'seeker' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/seeker[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'cv' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/cv[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Cv',
                        'action' => 'index',
                    ),
                ),
            ),
            'resume' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/resume/[:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                      
                    ),
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Registration',
                        'action' => 'resume',
                    ),
                ),
            ),
            'downloadcv' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/downloadcv/[:id]',
                    'constraints' => array(
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'Seeker\Controller\Registration',
                        'action' => 'downloadcv'
                    )
                )
            )
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'ReverseOAuth2\Google' => 'ReverseOAuth2\Client\GoogleFactory',
            'ReverseOAuth2\LinkedIn' => 'ReverseOAuth2\Client\LinkedInFactory',
            'ReverseOAuth2\Github' => 'ReverseOAuth2\Client\GithubFactory',
            'ReverseOAuth2\Facebook' => 'ReverseOAuth2\Client\FacebookFactory'
        ),
        'invokables' => array(
            'ReverseOAuth2\Auth\Adapter' => 'ReverseOAuth2\Authentication\Adapter\ReverseOAuth2',
        ),
    ),
    'reverseoauth2' => array(
        'google' => array(
            'scope' => array(
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/userinfo.email'
            ),
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://accounts.google.com/o/oauth2/token',
            'info_uri' => 'https://www.googleapis.com/oauth2/v1/userinfo',
            //local settings
            //'client_id'     => '252758738124-kjbe7svmas0urq0mqb4fb5gt40jb4ar2.apps.googleusercontent.com',
            //'client_secret' => 'MKzMM0KqgKcJEv1SwlLMSIzx',
            // dev settings 
            'client_id' => '211306747526-bq153om7fve1hj4hb377k3ld6m4vsms4.apps.googleusercontent.com',
            'client_secret' => '8_8m0tiQ1UHhVaEvuFluNRMW',
            'redirect_uri' => 'http://dev1.chakri.com/gmail',
            //'redirect_uri' => 'http://my.chakri.com/gmail',
        ),
        'facebook' => array(
            /* 'scope' => array(

              'user_about_me',
              'user_activities',
              'user_birthday',
              'read_friendlists',


              ), */
            'auth_uri' => 'https://www.facebook.com/dialog/oauth',
            'token_uri' => 'https://graph.facebook.com/oauth/access_token',
            'info_uri' => 'https://graph.facebook.com/me',
            'client_id' => '1587873978148110',
            'client_secret' => 'e4b343cebab618676b857016628d679a',
//local settings             
            //'client_id' => '1671182856431169',
           // 'client_secret' => '33afa10822891c030a1fed7162c9afe9',
            'redirect_uri' => 'http://dev1.chakri.com/registration/fb',
         //   'redirect_uri' => 'http://local.chakri.com/registration/fb',
        ),
        'linkedin' => array(
            'scope' => array(
                'r_basicprofile',
                'r_emailaddress'
               
            ),
            'auth_uri' => 'https://www.linkedin.com/uas/oauth2/authorization',
            'token_uri' => 'https://www.linkedin.com/uas/oauth2/accessToken',
            'info_uri' => 'https://api.linkedin.com/v1/people/~',
            // dev settings 
            
            'client_id'     => '759b88h2y0ljks',
            'client_secret' => '1oCKFmTePxQw2EK7',
            'redirect_uri' => 'http://dev1.chakri.com/registration/ln',
            //local settings 
            //'client_id' => '75gvx9zyejukj9',
            //'client_secret' => 'L1lyGSWz02CdIw58',
            
            //'redirect_uri' => 'http://my.chakri.com/registration/ln',
        ),
    )
);
