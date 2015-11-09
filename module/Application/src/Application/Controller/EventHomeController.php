<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class EventHomeController extends AbstractActionController
{
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $events=$EloquentDb::table('chk_event')
                ->select('*')
                ->where('status',1)
                ->where('event_date','>=',date("Y-m-d"))
                ->orderBy('event_date', "DESC")
                ->get();

        return new ViewModel(array('events'=>$events));
    }
    public function eventAction()
    {
        return new ViewModel();
    }
    public function eventdetailAction()
    {
        $event_id = $this->params()->fromRoute('id');
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $results = $EloquentDb::table('chk_event')
                ->select('*')
                ->where('id', $event_id)
                ->orderBy('event_date', "ASC")
                ->get();
        //echo "<pre>";
        //print_r($results);exit;
        return new ViewModel(array(
                                   'event'       => $results
                                ));
    }
    public function eventregistrationAction()
    {
        $error_messages=array();
        $success_messages=array();
        $request=$this->getRequest();
        if ($request->isPost()) {
            $eventReg=new \Application\Model\EventReg();
            if($eventReg::where('event_id','=',$request->getPost('event_id'),'and','email','=',$request->getPost('email'))->first()){
               $this->flashMessenger()->setNamespace('error')->addMessage('You already registered to this event.');
               return $this->redirect()->toUrl("/eventRegistration");
            }
            $form=new \Application\Form\EventForm();
            $form->setInputFilter($form->inputFilter);
            $post_data=$request->getPost();
            $form->setData($post_data);
            if($form->isValid()){
                $eventReg->id=$request->getPost('id');
                $eventReg->event_id=$request->getPost('event_id');
                $eventReg->name=$request->getPost('name');
                $eventReg->company_name=$request->getPost('company_name');
                $eventReg->address=$request->getPost('address');
                $eventReg->email=$request->getPost('email');
                $eventReg->website=$request->getPost('website');
                $eventReg->contact_no=$request->getPost('contact_no');
                $eventReg->note=$request->getPost('note');
    //            $event->code=$request->getPost('code');
    //            echo "<pre>";
    //            print_r($event->toArray());exit;
                $eventReg->save();
                //$this->flashMessenger()->setNamespace('success')->addMessage('Your request has been sent.');
                //$this->redirect()->toUrl('/registration/success');
                //return $this->redirect()->toUrl("/event");
            }else{
                foreach($form->inputFilter->getMessages() as $messageId=>$error)
                    foreach($error as $er)
                        $this->flashMessenger()->setNamespace('error')->addMessage(strtok(ucfirst($messageId),"_")." : ".$er);
            }
            $this->flashMessenger()->setNamespace('success')->addMessage('Your query has been sent successfully.');
            return $this->redirect()->toUrl("/event");
        }

//populate event dropdown 
        $event = new \Admin\Model\Event();
        $events = $event
                ->orWhere('event_date','>=',date("Y-m-d"))
                ->orderBy('event_date', "DESC")
                ->get();
//echo "<pre>";
//print_r($events->toArray());exit;
        return new ViewModel(array('error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'post_data' => $post_data,
            'events' => $events->toArray(),
            'id' =>$this->params()->fromRoute('id')
        ));
        //return new ViewModel();
    }
    public function eventarchivesAction()
    {
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $events=$EloquentDb::table('chk_event')
                ->select('*')
                ->where('status',0)
                ->orWhere('event_date','<',date("Y-m-d"))
                ->orderBy('event_date', "DESC")
                ->get();

        return new ViewModel(array('events'=>$events));
    }
    public function eventRegistrationCheckAction(){
        $request=$this->getRequest();
        $evt_reg_exists=false;

        if($request->isPost()){
            $event=$request->getPost('event');
            $email=$request->getPost('email');
        }

        $eventReg=new \Application\Model\EventReg();
        if($eventReg::where('event_id','=',$event,'and','email','=',$email)->first())
            $evt_reg_exists=true;

        $result=new JsonModel(array('success'=>$evt_reg_exists));

        return $result;
    }
}
