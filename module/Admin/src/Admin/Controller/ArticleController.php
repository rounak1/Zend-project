<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use yajra\Datatables\Datatables as Datatables;
use Intervention\Image\ImageManagerStatic as Image;

class ArticleController extends AbstractActionController{
    public function indexAction(){
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $articles=$EloquentDb::table('chk_article')
                ->select('chk_article.*','chk_users.full_name')
                ->leftJoin('chk_users','chk_article.created_by','=','chk_users.id')
                ->orderBy('created_at','DESC')
                ->get();

        return new ViewModel(array('articles'=>$articles));
    }

    public function addAction(){
        $request=$this->getRequest();
        $post_data=array_merge_recursive($request->getPost()->toArray(),$request->getFiles()->toArray());
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $form=new \Admin\Form\ArticleForm();
        $cats=$EloquentDb::table('chk_article_category')->select('id','title')->where('status',1)->get();
        $error_messages=array();

        if($request->isPost()){
            $article=new \Admin\Model\Article();
            $form->setInputFilter($form->inputFilter);

            if($article::where('title','=',$request->getPost('title'))->first()){
                $error_messages[]="This Article already exists.";
            }else{
                $post=array_merge_recursive($request->getPost()->toArray(),$request->getFiles()->toArray());
                $form->setData($post);

                if($form->isValid()){
                    if($post['article-image']['name']!= NULL){
                        $filename=stripslashes($post['article-image']['name']);
                        $extension=strtolower(pathinfo($filename,PATHINFO_EXTENSION));

                        if(($extension!="jpg") && ($extension!="jpeg") && ($extension!="png") && ($extension!="gif") && ($extension!="bmp")){
                            $this->flashMessenger()->setNamespace('error')->addMessage('Unknown image extension. You can upload the following extensions:jpg, jpeg, gif, png, bmp');
                            return $this->redirect()->toUrl("/admin/article/add");
                        }

                        $image_name=time().rand(100,110).'.'.$extension;
                        $newname="public/images/article/".$image_name;
                        $img=Image::make($post['article-image']['tmp_name']);
                        $img->save($newname);
                        $article->article_image=$image_name;
                    }

                    $article->title=$request->getPost('title');
                    $article->description=$request->getPost('description');
                    $article->category_id=implode(",",$request->getPost('cat')).",";
                    $article->status=$request->getPost('status');
                    $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
                    $article->created_by=$auth_users->id;
                    $article->save();

                    $this->flashMessenger()->setNamespace('success')->addMessage('Article has been added successfully.');
                    return $this->redirect()->toUrl("/admin/article");
                }else{
                    foreach($form->inputFilter->getMessages() as $messageId=>$error)
                        foreach($error as $key=>$er)
                            $error_messages[$key]=strtok(ucfirst($messageId),"_-")." : ".$er."<br/>";
                }
            }
        }

        $view=new ViewModel(array(
                'cats'=>$cats,
                'post_data'=>$post_data,
                'catsSelected'=>$request->getPost('cat'),
                'error_messages'=>$error_messages
            )
        );

        $view->setTemplate('admin/article/article-form.phtml');
        return $view;
    }

    public function editAction(){
        $request=$this->getRequest();
        $EloquentDb=$this->getServiceLocator()->get('EloquentZF2Adapter');
        $cats=$EloquentDb::table('chk_article_category')->select('id','title')->where('status',1)->get();

        $artcl=new \Admin\Model\Article();
        $id=$this->params()->fromRoute('id');
        $article=$artcl->find($id);
        $catsSelected=explode(",",$article['category_id']);

        if($request->isPost()){
            $form=new \Admin\Form\ArticleForm();
            $form->setInputFilter($form->inputFilter);
            $post=array_merge_recursive($request->getPost()->toArray(), $request->getFiles()->toArray());

            $post=array_merge_recursive($request->getPost()->toArray(),$request->getFiles()->toArray());
            $form->setData($post);

            if($form->isValid()){
                if($post['article-image']['name']!=NULL){
                    $filename=stripslashes($post['article-image']['name']);
                    $extension=$this->getExtension($filename);
                    $extension=strtolower($extension);

                    if(($extension!="jpg") && ($extension!="jpeg") && ($extension!="png") && ($extension!="gif") && ($extension!="bmp")){
                        $this->flashMessenger()->setNamespace('error')->addMessage('Unknown image extension. You can upload the following extensions:jpg, jpeg, gif, png, bmp');
                        return $this->redirect()->toUrl("/admin/article/edit/".$id);
                    }

                    $image_name=time().rand(100,110).'.'.$extension;
                    $newname="public/images/article/".$image_name;
                    $img=Image::make($post['article-image']['tmp_name']);
                    $img->save($newname);
                    $article->article_image=$image_name;
                }
                $article->title=$request->getPost('title');
                $article->description=$request->getPost('description');
                $article->category_id=implode(",",$request->getPost('cat')).",";
                $article->status=$request->getPost('status');
                $auth_users=$this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $article->created_by=$auth_users->id;
                $article->save();
                $this->flashMessenger()->setNamespace('success')->addMessage('Article has been updated successfully.');
                return $this->redirect()->toUrl("/admin/article");
            }else{
                foreach($form->inputFilter->getMessages() as $messageId=>$error)
                    foreach($error as $er)
                        $this->flashMessenger()->setNamespace('error')->addMessage(strtok(ucfirst($messageId),"-")." : ".$er);
            }
        }

        $view=new ViewModel(array('cats'=>$cats,'article'=>$article,'catsSelected'=>$catsSelected));
        return $view;
    }

    public function deleteAction(){
        $id=$this->params()->fromRoute('id');
        $article=new \Admin\Model\Article();

        $results=$article->find($id);
        $results->status=0;
        $results->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Article has been deleted.');
        return $this->redirect()->toUrl("/admin/article");
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