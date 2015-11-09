<?php

namespace Chkapi\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CvrestController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function contactAction ()
    {
        return new ViewModel();
    }


}

