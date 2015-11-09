<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Intervention\Image\ImageManagerStatic as Image;
//import Size validator...
use Zend\Validator\File\Size;
use Carbon\Carbon;


class InstituteController extends AbstractActionController {

    public function indexAction() {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $results = $EloquentDb::table('chk_institute')
                ->select('chk_institute.*', 'chk_users.full_name')
                ->leftJoin('chk_users', 'chk_institute.modified_by', '=', 'chk_users.id')
                ->where('chk_institute.status', '=', '1')
                ->orderBy('chk_institute.name', 'ASC')
                ->get();

        //$this->layout('layout/admin');

        return new ViewModel(array('institue' => $results));
    }

    public function editAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $institue = new \Admin\Model\Institute();

        //$institue = new \Admin\Model\Institute();
        $error_messages = array();

        $post_data = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
        );
        
      

        if ($request->isPost()) {  
//            if ($institue::where('name', '=', $request->getPost('iname'))->first()) {
//               $this->flashMessenger()->setNamespace('error')->addMessage('This Institute/University already exist.');
//                return $this->redirect()->toUrl("/admin/institute");
//            }else{
            
            if ($post_data['image_upload'] != '') {
                    $filename = stripslashes($post_data['image_upload']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);

                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[] = "Unknown image extension. <br>
                                            You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 0;
                    } else {
                        //$size = new Size(array('min' => 2000000)); //minimum bytes filesize
                        $size = new Size(array('max' => '1MB')); //minimum bytes filesize 
                        $adapter = new \Zend\File\Transfer\Adapter\Http();
                        $adapter->setValidators(array($size), $filename);

                        if (!$adapter->isValid()) {

                            $dataError = $adapter->getMessages();
                            $error = array();
                            foreach ($dataError as $key => $row) {
                                $error_messages[] = $row;
                            }

                            // $form->setMessages(array('fileupload' => $error));

                            $is_validation_error = 0;
                        } else {


                            $image_name = time() . '.' . $extension;
                            $publicDir = getcwd() . '/public';


                            $destination = $publicDir . '/upload/cv';
                            $new_file_name = '/' . uniqid() . $post_data['image_upload']['name'];
                            $path_filename = $post_data['image_upload']['name'];
                            $name_prefix = substr($new_file_name, 0, strrpos($new_file_name, ".$extenstion"));
                            $medium_image = $name_prefix . '_medium.' . $extension;
                            $thumbnail_image = $name_prefix . '_thumbnail.' . $extension;


                            $img = Image::make($post_data['image_upload']['tmp_name'])
                                            ->resize(150, 150)->save($destination . $medium_image)
                                            ->resize(50, 50)->save($destination . $thumbnail_image)
                            ;
                            $img->save($destination . $new_file_name);
                        }
                    }
                }
            
            $institueVal = $institue->find($id);
            $institueVal->name = $request->getPost('iname');
            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
            $institueVal->modified_by = $auth_users->id;
            $institueVal->about_courses = $request->getPost('about_courses');
            $institueVal->is_top = $request->getPost('is_top');
            $institueVal->organigation_status = $request->getPost('organigation_status');
               if ($medium_image != '') {
                    $institueVal->institute_logo = '/upload/cv'.$medium_image;
                }
                
            //    print_r($medium_image);exit;
            $institueVal->save();

            $this->flashMessenger()->setNamespace('success')->addMessage('This Institute/University has been updated');
            return $this->redirect()->toUrl("/admin/institute");
//            }
        } else {
            $results = $institue->find($id);
        }

        return new ViewModel(array('results' => $results));
    }

    public function addAction() {
        $request = $this->getRequest();
        //$post_data = $request->getPost();
        $institue = new \Admin\Model\Institute();
        $error_messages = array();

        $post_data = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
        );

        if ($request->isPost()) {
            if ($institue::where(array('name' => $request->getPost('iname'), 'status' => 1))->first()) {
                $error_messages[] = "This Institute/University already exists.";
            } else {
                
                

                if ($post_data['image_upload'] != '') {
                    $filename = stripslashes($post_data['image_upload']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);
                    
                    
                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[] = "Unknown image extension. <br>
                                            You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 0;
                    } else {
                        //$size = new Size(array('min' => 2000000)); //minimum bytes filesize
                        $size = new Size(array('max' => '1MB')); //minimum bytes filesize 
                        $adapter = new \Zend\File\Transfer\Adapter\Http();
                        $adapter->setValidators(array($size), $filename);
                        
                        
                        
                        if (!$adapter->isValid()) {

                            $dataError = $adapter->getMessages();
                            $error = array();
                            foreach ($dataError as $key => $row) {
                                $error_messages[] = $row;
                            }

                            // $form->setMessages(array('fileupload' => $error));

                            $is_validation_error = 0;
                        } else {


                            $image_name = time() . '.' . $extension;
                            $publicDir = getcwd() . '/public';
                            

                            $destination = $publicDir . '/upload/cv';
                            $new_file_name = '/' . uniqid() . $post_data['image_upload']['name'];
                            $path_filename = $post_data['image_upload']['name'];
                            $name_prefix = substr($new_file_name, 0, strrpos($new_file_name, ".$extenstion"));
                            $medium_image = $name_prefix . '_medium.' . $extension;
                            $thumbnail_image = $name_prefix . '_thumbnail.' . $extension;

                            
                            $img = Image::make($post_data['image_upload']['tmp_name'])
                                            ->resize(150, 150)->save($destination . $medium_image)
                                            ->resize(50, 50)->save($destination . $thumbnail_image)
                            ;
                            $img->save($destination . $new_file_name);
                        
                            
                            
                        }
                    }
                }






                $institue->name = $request->getPost('iname');
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                $institue->created_by = $auth_users->id;
                $institue->about_courses = $request->getPost('about_courses');
                $institue->is_top = $request->getPost('is_top');
                $institue->organigation_status = $request->getPost('organigation_status');
                if ($medium_image != '') {
                    $institue->institute_logo = '/upload/cv'.$medium_image;
                }

                $institue->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Institute/University has been added.');
                return $this->redirect()->toUrl("/admin/institute");
            }
        }

        return new ViewModel(array(
            'post_data' => $post_data,
            'error_messages' => $error_messages
                )
        );
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $institue = new \Admin\Model\Institute();

        $institueData = $institue->find($id);
        $institueData->status = 0;
        $institueData->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('This Institute/University has been deleted.');
        return $this->redirect()->toUrl("/admin/institute");
    }

    public function instcheckAction() {
        $request = $this->getRequest();
        $inst_exists = false;
        if ($request->isPost())
            $inst = $request->getPost('inst');

        $sm = $this->getServiceLocator();
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $validator = new \Zend\Validator\Db\RecordExists(array(
            'table' => 'chk_institute',
            'field' => 'name',
            'adapter' => $dbAdapter
                )
        );

        if ($validator->isValid($inst))
            $inst_exists = true;

        $result = new JsonModel(array('inst' => $inst, 'success' => $inst_exists));

        return $result;
    }

    public function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

}
