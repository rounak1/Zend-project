<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// datatables 
//use yajra\Datatables\DatatablesServiceProvider;
use yajra\Datatables\Datatables as Datatables;


class FeedController extends AbstractActionController {
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $feedback=$EloquentDb::table('chk_feedback')
                ->select('*')
                ->orderBy('created_at','DESC')
                ->get();

        return new ViewModel(array('feedbacks'=>$feedback));
    }
    
    public function deleteAction(){
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $EloquentDb::table('chk_feedback')->where('id', $id)->delete();        

        $this->flashMessenger()->setNamespace('success')->addMessage('Feedback has been deleted.');
        return $this->redirect()->toUrl("/admin/feed"); 
    }
    public function showAction(){
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $feedback = new \Admin\Model\Feedback();
        $results = $feedback->find($id);
        return new ViewModel(array('feed' => $results)); 
    }
    public function getExtension($str){
        $i=strrpos($str,".");
        if(!$i)
            return "";
        $l=strlen($str)-$i;
        $ext=substr($str,$i+1,$l);
        return $ext;
    }
}