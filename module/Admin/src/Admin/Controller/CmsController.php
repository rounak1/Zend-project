<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CmsController extends AbstractActionController {
    
    public function indexAction() {        
        $EloquentDb=  $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
        
        $results =  $EloquentDb::table('chk_pages')
        ->select('*')        
        ->where('trashed','=', '0')      
        ->get();
                       
        return new ViewModel(array('results' => $results));
    }
    
    public function addAction(){
        
        $error_messages   = array();
        $success_messages = array();
        $result           =  array();
        
        $request = $this->getRequest();
        $post_data = $request->getPost();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        if ($request->isPost()) {
            
            $is_validation_error = 0;
            
            $cmsForm = new \Admin\Form\CmsForm();
            
            $cmsForm->inputFilter->setData($request->getPost()->toArray());
            
            $sm = $this->getServiceLocator();
            
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            
            $validator = new \Zend\Validator\Db\RecordExists(
                        array(
                                'table' => 'chk_pages',
                                'field' => 'alias',
                                'adapter' => $dbAdapter
                            )
                        );
            
            
            if ($validator->isValid($request->getPost('alias'))) {
                
                $is_validation_error = 1;
                $error_messages[]    = 'Alias Already exists';
            }
            
            if (($is_validation_error == 0) && $cmsForm->inputFilter->isValid()) {
                
                $EloquentDb::table('chk_pages')->insert(
                                        [
                                            'title'            => $request->getPost('title'), 
                                            'alias'            => $request->getPost('alias'), 
                                            'meta_key'         => $request->getPost('meta_key'), 
                                            'meta_description' => $request->getPost('meta_description'), 
                                            'content'          => $request->getPost('content')
                                        ]
                                    );
                
                $this->flashMessenger()->setNamespace('success')->addMessage('Successfully add your content.');
                return $this->redirect()->toUrl("/admin/cms");
            }
            else {

                foreach ($cmsForm->inputFilter->getInvalidInput() as $messageId => $error) {
                    $err = $error->getMessages();

                    foreach ($err as $er) {
                        $error_messages[] = $messageId . " : " . $er;
                    }
                }
            }
        }        
        
        return new ViewModel(array(
            'post_data'      => $post_data,
            'result'         => $result,
            'error_messages' => $error_messages            
        ));
    }
    
    public function editAction() {
        
        $error_messages   = array();
        $success_messages = array();
        $result           =  array();
        
        $request   = $this->getRequest();
        $post_data = $request->getPost();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $id = $this->params()->fromRoute('id');
        
        if ($request->isPost()) {
            
            $is_validation_error = 0;
            
            $cmsForm = new \Admin\Form\CmsForm();
            $cmsForm->inputFilter->setData($request->getPost()->toArray());
            
            if ($this->isAliasValid($request->getPost('alias'), $id)) {
                $error_messages[] = "Alias name Exists";
                $is_validation_error = 1;
            }
            
            if (($is_validation_error == 0) && $cmsForm->inputFilter->isValid()) {
                
                $EloquentDb::table('chk_pages')
                            ->where('id', $id)
                            ->update(array(
                                            'title'            => $request->getPost('title'), 
                                            'alias'            => $request->getPost('alias'), 
                                            'meta_key'         => $request->getPost('meta_key'), 
                                            'meta_description' => $request->getPost('meta_description'), 
                                            'content'          => $request->getPost('content')
                                          ));
                
                $this->flashMessenger()->setNamespace('success')->addMessage('Successfully update your content.');
                return $this->redirect()->toUrl("/admin/cms");
                
            }
            else {
                
                /*
                $messages = $this->getErrorMessages($cmsForm->inputFilter);  
                if (count($messages) > 0) {
                    $error_messages = $messages;
                }
                */      
                foreach ($cmsForm->inputFilter->getInvalidInput() as $messageId => $error) {
                    $err = $error->getMessages();

                    foreach ($err as $er) {
                        $error_messages[] = $messageId . " : " . $er;
                    }
                }
            }            
        }
        else{
            
            $result =  $EloquentDb::table('chk_pages')
            ->select('*')
            ->where('id','=', $id)         
            ->where('trashed','=', '0')      
            ->first();            
        }
              
        $view = new ViewModel(array(
            'post_data'      => $post_data,
            'result'         =>  $result,
            'error_messages' => $error_messages
        ));

        $view->setTemplate('admin/cms/add.phtml');
        return $view;
    }
    
    public function isAliasValid($alias = "", $id = "") {

        $alias_exists = false;
        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

        $validator = new \Zend\Validator\Db\RecordExists(
                        array(
                                'table' => 'chk_pages',
                                'field' => 'alias',
                                'adapter' => $dbAdapter,
                                'exclude' => array(
                                    'field' => 'id',
                                    'value' => $id
                                )
                        )
                    );


        if ($validator->isValid($alias)) {
            $alias_exists = 1;
        }

        return $alias_exists;
    }
    
    public function deleteAction() {
        
        $id = $this->params()->fromRoute('id');        
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $EloquentDb::table('chk_pages')
                            ->where('id', $id)
                            ->update(array('trashed' => 1));

        $this->flashMessenger()->setNamespace('success')->addMessage('Your content has been deleted.');
        return $this->redirect()->toUrl("/admin/cms");
    }
    
}