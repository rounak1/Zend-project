<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class ArticleCategoryController extends AbstractActionController{
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $categories=$EloquentDb::table('chk_article_category')
                                ->select('chk_article_category.*','chk_users.full_name')
                                ->leftJoin('chk_users','chk_article_category.created_by','=','chk_users.id')
                                ->where('status',1)
                                ->orderBy('created_at','DESC')
                                ->get();
        return new ViewModel(array('categories'=>$categories));
    }

    public function addAction(){
        $request=$this->getRequest();
        $post_data=$request->getPost();
        $category=new \Admin\Model\ArticleCategory();
        $error_messages=array();

        if($request->isPost()){
            if($category::where('title','=',$request->getPost('title'))->first()){
               $error_messages[]="This Category title already exists.";
            }else{
               $category->title=$request->getPost('title');
               $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
               $category->created_by=$auth_users->id;
               $category->save();
               $this->flashMessenger()->setNamespace('success')->addMessage('Category has been added successfully.');
               return $this->redirect()->toUrl("/admin/articleCategory");
            }
        }

        return new ViewModel(array(
                'post_data'=>$post_data,
                'error_messages'=>$error_messages
            )
        );
    }

    public function editAction(){
        $request=$this->getRequest();
        $id=$this->params()->fromRoute('id');
        $cat=new \Admin\Model\ArticleCategory();
        $category=$cat->find($id);

        if($request->isPost()){
            $category->title=$request->getPost('title');
            $category->status=$request->getPost('status');
            $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $category->created_by=$auth_users->id;
            $category->save();
            $this->flashMessenger()->setNamespace('success')->addMessage('Update Successful');
            return $this->redirect()->toUrl("/admin/articleCategory");
        }

        return new ViewModel(array('cat'=>$category));
    }

    public function deleteAction(){
        $id=$this->params()->fromRoute('id');
        $cat=new \Admin\Model\ArticleCategory();

        $category=$cat->find($id);
        $category->status=0;
        $category->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Article Category has been deleted.');
        return $this->redirect()->toUrl("/admin/articleCategory");
    }

    public function titlecheckAction(){
        $request=$this->getRequest();
        $title_exists=false;
        if($request->isPost())
            $title=$request->getPost('title');

        $sm=$this->getServiceLocator();
        $dbAdapter=$sm->get('Zend\Db\Adapter\Adapter');
        $validator=new \Zend\Validator\Db\RecordExists(array(
                        'table'=>'chk_article_category',
                        'field'=>'title',
                        'adapter'=>$dbAdapter
                    )
        );

        if($validator->isValid($title))
            $title_exists=true;

        $result=new JsonModel(array('title'=>$title,'success'=>$title_exists));

        return $result;
    }
}