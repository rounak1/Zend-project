<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $e->getApplication()->getServiceManager()->get('translator');
        $e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('currentRequest', function($sm) use ($e) {
            $viewHelper = new View\Helper\CurrentRequest($e->getRouteMatch());
            return $viewHelper;
        });

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        date_default_timezone_set('UTC');
    }

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

    public function getViewHelperConfig() {
        return array(
            'factories' => array(
                'custom_route_helper' => function($sm) {
                    $helper = new View\Helper\CustomRouteHelper;
                    return $helper;
                },
                'seeker_helper' => function($sm) {
                    $helper = new View\Helper\SeekerHelper;
                    return $helper;
                },
                'job_helper' => function($sm) {
                    $helper = new View\Helper\JobHelper;
                    return $helper;
                },
                'current_request_helper' => function ($sm) {
                    $helper = new View\Helper\CurrentRequest;
                    return $helper;
                },
                'get_last_expereince' => function ($sm) {
                    $helper = new View\Helper\ExperienceHelper;
                    return $helper;
                },
                'notification_helper' => function ($sm) {
                    $helper = new View\Helper\NotificationHelper;
                    return $helper;
                },
                'permission_helper' => function ($sm) {
                    $helper = new View\Helper\PermissionHelper;
                    return $helper;
                },
                'home_helper' => function ($sm) {
                    $helper = new View\Helper\HomeHelper;
                    return $helper;
                }
            )
        );
    }

}
