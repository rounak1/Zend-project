<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Carbon\Carbon;
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;

class ArticleController extends AbstractActionController{
    public function indexAction(){
        $cid = $this->params()->fromRoute('cid');
        
        $EloquentDb = $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');

        $cats = $EloquentDb::table('chk_article_category')
                ->select('id', 'title')
                ->where('status', 1)
                ->orderBy('title', "ASC")
                ->get();

        if ($cid) {
            $results = $EloquentDb::table('chk_article')
                    ->select('*')
                    ->where('category_id', "like", "%" . $cid . ",%")
                    ->where('status',1)
                    ->orderBy('created_at', "DESC")
                    ->get();
            $active = $cid;
        } else {
            $results = $EloquentDb::table('chk_article')
                    ->select('*')
                    ->where('status',1)
                    ->orderBy('created_at', "DESC")
                    ->get();
            $active = "home";
        }

        $padapter = new ArrayAdapter($results);
        $paginator = new Paginator($padapter);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page',1))
                ->setItemCountPerPage(10)->setPageRange(5);

        return new ViewModel(array(
            'cats' => $cats, 
            'active' => $active,
            'paginator' => $paginator
        ));
    }

    public function articleDetailsAction(){
        $id=$this->params()->fromRoute('id');
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');

        $results=$EloquentDb::table('chk_article')->select('*')->where('id',$id)->orderBy('id',"DESC")->get();
        $results[0]['created_at']=Carbon::createFromFormat('Y-m-d h:i:s',$results[0]['created_at'])->toFormattedDateString();

        $cats=$EloquentDb::table('chk_article_category')->select('id','title')->where('status',1)->orderBy('title',"ASC")->get();

        return new ViewModel(array('article'=>$results,'cats'=>$cats));
    }
}