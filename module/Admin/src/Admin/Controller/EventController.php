<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// datatables 
//use yajra\Datatables\DatatablesServiceProvider;
use yajra\Datatables\Datatables as Datatables;
// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

class EventController extends AbstractActionController {
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $events=$EloquentDb::table('chk_event')
                ->select('*')
                ->orderBy('event_date','DESC')
                ->get();
        return new ViewModel(array('events'=>$events));
    }

    public function addAction() {
        $error_messages=array();
        $is_validation_error=0;
        $request=$this->getRequest();
        $post_data = $request->getPost();
        $event=new \Admin\Model\Event();

        if($request->isPost()){
            if($event::where(array('event_title'=>$request->getPost('event_title')))->first()){
                $error_messages[]='This Event already exists.';
            }else{
                $post=array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());
                $image_name="";
                $extension="";
                if($post['event_logo']['name']==""){
                    $error_messages[]="You must upload an image file with one<br>
                                       of the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error=1;
                }else{
                    $filename=stripslashes($post['event_logo']['name']);
                    $extension=$this->getExtension($filename);
                    $extension=strtolower($extension);

                    if(($extension!="jpg") && ($extension!="jpeg") && ($extension!="png") && ($extension!="gif") && ($extension!="bmp")){
                        $error_messages[]="Unknown image extension.<br>
                                           You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error=1;
                    }
                }
                if($is_validation_error==0){
                    if($post['event_logo']['name']!=""){
                        $image_name=time().rand(100,110).'.'.$extension;
                        $newname="public/images/event/".$image_name;
                        $img=Image::make($post['event_logo']['tmp_name']);
                        $img->fit(900,200);
                        $img->save($newname);
                    }
                    $event->event_title=$request->getPost('event_title');
                    $event->event_logo=$image_name;
                    $event->program_type=$request->getPost('program_type');
                    $event->venue=$request->getPost('venue');
                    $event->event_date=$request->getPost('event_date');
                    $event->event_expire_date=$request->getPost('event_expire_date');
                    $event->event_conduct_by=$request->getPost('event_conduct_by');
                    $event->details=$request->getPost('details');
                    $event->benefits=$request->getPost('benefits');
                    $event->url=$request->getPost('url');
                    $event->lastRegiDate=$request->getPost('lastRegiDate');
                    $event->status=$request->getPost('status');
                    $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
                    $event->created_by=$auth_users->id;
                    $event->save();
                    $this->flashMessenger()->setNamespace('success')->addMessage('Event has been added.');
                    return $this->redirect()->toUrl("/admin/event");
                }
            }
        }

        return new ViewModel(array('error_messages'=>$error_messages,'post_data'=>$post_data));
    }

    public function editAction(){
        $request=$this->getRequest();
        $id=$this->params()->fromRoute('id');
        $evt=new \Admin\Model\Event();
        $event=$evt->find($id);
        $form=new \Admin\Form\EventForm();

        if($request->isPost()){
            $form->setInputFilter($form->inputFilter);
            $post=array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());

            $form->setData($post);
            if($form->isValid()){
                if($post['article-image']['name']!=NULL){
                    $filename=stripslashes($post['event_logo']['name']);
                    $extension=$this->getExtension($filename);
                    $extension=strtolower($extension);

                    if(($extension!="jpg") && ($extension!="jpeg") && ($extension!="png") && ($extension!="gif") && ($extension!="bmp")){
                        $this->flashMessenger()->setNamespace('error')->addMessage('Unknown image extension. You can upload the following extensions:jpg, jpeg, gif, png, bmp');
                        return $this->redirect()->toUrl("/admin/event/edit/".$id);
                    }

                    $image_name=time().rand(100,110).'.'.$extension;
                    $newname="public/images/event/".$image_name;
                    $img=Image::make($post['event_logo']['tmp_name']);
                    $img->save($newname);
                    $event->event_logo=$image_name;
                }
                $event->event_title=$request->getPost('event_title');
                $event->program_type=$request->getPost('program_type');
                $event->venue=$request->getPost('venue');
                $event->event_date=$request->getPost('event_date');
                $event->event_expire_date=$request->getPost('event_expire_date');
                $event->event_conduct_by=$request->getPost('event_conduct_by');
                $event->details=$request->getPost('details');
                $event->benefits=$request->getPost('benefits');
                $event->url=$request->getPost('url');
                $event->lastRegiDate=$request->getPost('lastRegiDate');
                $event->status=$request->getPost('status');
                $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $event->created_by=$auth_users->id;
                $event->save();
                $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
                return $this->redirect()->toUrl("/admin/event");
            }else{
                foreach($form->inputFilter->getMessages() as $messageId=>$error)
                    foreach($error as $er)
                        $this->flashMessenger()->setNamespace('error')->addMessage(strtok(ucfirst($messageId),"_-")." : ".$er);
                return $this->redirect()->toUrl("/admin/event/edit/".$id);
            }
        }

        return new ViewModel(array('event'=>$event));
    }

    public function deleteAction(){
        $id=$this->params()->fromRoute('id');
        $cat=new \Admin\Model\Event();

        $results=$cat->find($id);
        $results->status = 0;
        $results->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Event has been deleted.');
        return $this->redirect()->toUrl("/admin/event");
    }

    public function evtregAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $events =  $EloquentDb::table('chk_event_registration')
        ->select('chk_event_registration.*', 'chk_event.event_title')
        ->leftJoin('chk_event', 'chk_event.id', '=', 'chk_event_registration.event_id')      
        ->get();

        return new ViewModel(array('events'=>$events));
    }

    public function eventDeleteAction(){
        $request=$this->getRequest();
        $id=$this->params()->fromRoute('id');
        $reg=new \Admin\Model\EventRegistration();

        $results=$reg->find($id);
        $results->delete();

        $this->flashMessenger()->setNamespace('success')->addMessage('Event Registration has been deleted.');
        return $this->redirect()->toUrl("/admin/evtreg");
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