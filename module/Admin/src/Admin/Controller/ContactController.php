<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// datatables 
//use yajra\Datatables\DatatablesServiceProvider;
use yajra\Datatables\Datatables as Datatables;

class ContactController extends AbstractActionController {

    public function indexAction(){
        $total_contact_count = \Admin\Model\Contact::count();
        $resolved = \Admin\Model\Contact::where('view_status','=',1)->count();
        $pending = \Admin\Model\Contact::where('view_status','=',0)->count();
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $results =  $EloquentDb::table('chk_contact_us')
                    ->select('chk_contact_us.*','chk_users.full_name')
                    ->leftJoin('chk_users','chk_contact_us.resolved_by','=','chk_users.id')
                    ->orderBy('created_at','DESC')
                    ->get();        

        return new ViewModel(array(
            'contacts'=> $results,
            'total_contact'=> $total_contact_count,
            'resolved'=>$resolved,
            'pending'=>$pending
        ));
    }

    public function showAction(){
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $contact = new \Admin\Model\Contact();
        $results = $contact->find($id);

        $request = $this->getRequest();
        if($request->isPost()){
            $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();            
            $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
            $EloquentDb::table('chk_contact_us')
                    ->where('id',$id)
                    ->update(array(
                        'view_status'=>$request->getPost('view_status'), 
                        'resolved_by' => $auth_users->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        ));

            $this->flashMessenger()->setNamespace('success')->addMessage('The query has been resolved.');
            return $this->redirect()->toUrl("/admin/contact");
        }

        return new ViewModel(array('contact' => $results));
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $contact = new \Admin\Model\Contact();

        $results = $contact->find($id);        
        $results->delete();

        $this->flashMessenger()->setNamespace('success')->addMessage('Contact Inquery has been deleted.');
        return $this->redirect()->toUrl("/admin/contact");
    }
}