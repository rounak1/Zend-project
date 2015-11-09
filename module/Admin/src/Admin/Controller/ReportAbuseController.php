<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use yajra\Datatables\Datatables as Datatables;

class ReportAbuseController extends AbstractRestfulController{
//$queries =  $EloquentDb::getQueryLog();
//$last_query = end($queries);
//echo "<pre>";
//print_r($last_query['query']);
//exit ();
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $reports=$EloquentDb::table('chk_report_abuse')
                ->select('chk_report_abuse.*','chk_users.full_name as viewed_by',
                        'chk_users.email as member_email')
                ->leftJoin('chk_users','chk_report_abuse.viewed_by','=','chk_users.id')
                ->orderBy('created_at','DESC')
                ->get();

        return new ViewModel(array('reports'=>$reports));
    }

    public function showAction(){
        $id = $this->params()->fromRoute('id');
        $report = new \Admin\Model\ReportAbuse();
        $result = $report->find($id);

        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        $viewed_by = ($auth_users->id) ? $auth_users->id : NULL;
        $result->viewed_by = $viewed_by;
        $result->save();

        return new ViewModel(array('report'=>$result)); 
    }

    public function deleteAction(){
        $id=$this->params()->fromRoute('id');
        $report=new \Admin\Model\ReportAbuse();

        $results=$report->find($id);
        $results->status=0;
        $results->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Report has been deleted.');
        return $this->redirect()->toUrl("/admin/report-abuse");
    }
}