<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// datatables 
//use yajra\Datatables\DatatablesServiceProvider;
use yajra\Datatables\Datatables as Datatables;
// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

class JobController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function newsjobAction() {
        return new ViewModel();
    }

    public function govtJobAction() {
        return new ViewModel();
    }

    public function spotlightJobAction() {
        return new ViewModel();
    }

    public function newsJobAddAction() {

        $post_data              = array();
        $is_validation_error    = 0;
        $error_messages         = array();
        $success_messages       = array();
        $selected_job_circular  = array();
        $selected_categories_id = array();
        $selected_industry_id   = array();
        
        //Default selected newspaper job
        $selected_job_circular[0] = 1;
        
        
        $request             = $this->getRequest();
        $job = new \Admin\Model\Job();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id', 'type')
                ->where ('status','=',1)
                
                ->get();
        
        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();

        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();
            
            $post = array_merge_recursive(
                        $request->getPost()->toArray(), 
                        $request->getFiles()->toArray()
                    );
            
            if ($post['image-file']['name'] != ""){
                
                $filename  = stripslashes($post['image-file']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                    $error_messages[]    = "Unknown image extension. <br>
                                            You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                }
            }
            
            //For spot light
            if($request->getPost('spot_light_used') == 1)
            {                
                if ($post['spotlight_job_logo']['name'] == "") {
                    $error_messages[]    = "You must upload an image file for spotlight job with one<br>
                                           of the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                }
                else
                {
                    $spot_filename  = stripslashes($post['spotlight_job_logo']['name']);
                    $spot_extension = $this->getExtension($spot_filename);
                    $spot_extension = strtolower($spot_extension);
                                     
                    if (($spot_extension != "jpg") && ($spot_extension != "jpeg") && ($spot_extension != "png") && ($spot_extension != "gif") && ($spot_extension != "bmp")) {
                        $error_messages[]    = "Spot light images unknown extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }
                
                if($request->getPost('spotlight_job_content') == "")
                {
                    $error_messages[]    = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_order') == "")
                {
                    $error_messages[]    = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_gorup') == "")
                {
                    $error_messages[]    = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }

            // create job form to validation 
            $newspaperJobForm = new \Admin\Form\NewspaperJobForm();
            
            $newspaperJobForm->inputFilter->setData($request->getPost()->toArray());

            //if (($is_validation_error == 0) && ($newspaperJobForm->inputFilter->isValid())) {
            if ($is_validation_error == 0) {
                
                if ($post['image-file']['name'] != "") {                  

                    $image_name = time().rand(100, 110) . '.' . $extension;
                    $newname    = "public/upload/job/" . $image_name;
                    $copied     = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);                    
                }
                
                //Spotlight
                if($request->getPost('spot_light_used') == 1)
                {
                    $spot_image_name = time() . '.' . $spot_extension;
                    $spot_newname    = "public/upload/job/" . $spot_image_name;
                    $copied          = copy($post['spotlight_job_logo']['name'], $spot_newname);

                    $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($spot_newname);      
                   
                    $job->spotlight_job_content = $request->getPost('spotlight_job_content');
                    $job->spotlight_job_logo    = $spot_image_name;
                    $job->spotlight_job_order   = $request->getPost('spotlight_job_order');
                    $job->spotlight_job_gorup   = $request->getPost('spotlight_job_gorup');
                }
                

                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                                
                $job->created_by          = $auth_users->id;
                $job->created_at          = date("Y-m-d H:i:s");
                $job->job_company_name    = $request->getPost('job_company_name');
                $job->company_description = $request->getPost('company_description');
                $job->job_title           = $request->getPost('job_title');
                //$job->job_industry_id     = implode(",", $request->getPost('job_industry_id'));
                //$job->job_category_id     = implode(",", $request->getPost('job_category_id'));
                $job->job_level           = $request->getPost('job_level');
                $job->job_type            = $request->getPost('job_type');

                $job->job_location = $request->getPost('job_location');
                $job->no_of_vacencies = $request->getPost('no_of_vacencies');
                $job->job_requirement = $request->getPost('job_requirement');
                $job->job_description = $request->getPost('job_description');
                $job->job_education = $request->getPost('job_education');
                $job->job_experienced = $request->getPost('job_experienced');
                //$job->age_limit_from = $request->getPost('age_limit_from');
                $job->salary_range = $request->getPost('salary_range');
                $job->direct_url = $request->getPost('direct_url');
                $job->job_apply_instruction = $request->getPost('job_apply_instruction');
                $job->compnay_address = $request->getPost('compnay_address');
                $job->other_information = $request->getPost('other_information');
                $job->published = $request->getPost('published');
                $job->posting_date = $request->getPost('posting_date');
                $job->job_deadline = $request->getPost('job_deadline');
                $job->spot_light_used     = $request->getPost('spot_light_used');
                $job->job_circular_type     =2;
                
                /*
                if ($request->getPost('govtJobStatus') == 1) {
                    $govtJobStatus = "1,2";
                } else {
                    $govtJobStatus = "1";
                }

                $job->job_circular_type = $govtJobStatus;
                $job->walk_in_active = $request->getPost('walk_in_active');
                */
                
                //Age limit
                if($_POST["type_age"] == "type_age")
                {     
                    $job->age_limit_other = $request->getPost('age_limit_other');
                    $job->age_limit_to    = "";
                    $job->age_limit_from  = "";
                }
                else
                {
                   $job->age_limit_other = "";
                   $job->age_limit_to    = $request->getPost('age_limit_to');
                   $job->age_limit_from  = $request->getPost('age_limit_from');
                }
                
                
                $job->status         = $request->getPost('status');
                $job->job_image      = $image_name;

                $job->save();
                $job_id = $job->id;
                
                $this->updateSpotlightJobId($job_id);
                
                //job industry add
                foreach($request->getPost('job_industry_id') as $industry_id)
                {
                   $EloquentDb::table('chk_job_industry_map')->insert(
                                        [
                                            'job_id'      => $job_id, 
                                            'industry_id' => $industry_id
                                        ]
                                    );
                }
                
                //job category add
                foreach($request->getPost('job_category_id') as $category_id)
                {
                   $EloquentDb::table('chk_job_category_map')->insert(
                                        [
                                            'job_id'      => $job_id, 
                                            'category_id' => $category_id
                                        ]
                                    );
                }
                
                //job circular add
                foreach($request->getPost('job_circular_id') as $job_circular_id)
                {
                   $EloquentDb::table('chk_job_circular_type')->insert(
                                        [
                                            'job_id'          => $job_id, 
                                            'job_circular_id' => $job_circular_id
                                        ]
                                    );
                }

                $this->flashMessenger()->setNamespace('success')->addMessage('Newspaper job has been added.');
                return $this->redirect()->toUrl("/admin/job/newsjob");
            } else {

                $messages = $this->getErrorMessages($newspaperJobForm->inputFilter);
                if (count($messages) > 0) {
                    $error_messages = $messages;
                }
            }
        }

        
        return new ViewModel(array(
                                   'results' => $results,
                                    'error_messages' => $error_messages,
                                    'success_messages' => $success_messages,
                                    'post_data' => $post_data,
                                    'job_circular' => $job_circular,
                                    'job_industry' => $job_industry,
                                    'job_category' => $job_category,
                                    'selected_job_circular'  => $selected_job_circular,
                                    'selected_categories_id' => $selected_categories_id,
                                    'selected_industry_id'   => $selected_industry_id                
                                ));
        
    }
    
    function newsjobeditAction() {

        $post_data              = array();
        $is_validation_error    = 0;
        $error_messages         = array();
        $success_messages       = array();
        $selected_categories_id = array();
        $selected_industry_id   = array();
        $request                = $this->getRequest();
        
        
        $id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Delete previous job images
        $job_images =  $EloquentDb::table('chk_jobs') 
                                        ->select('job_image','spotlight_job_logo') 
                                        ->where('id', $id)                        
                                        ->first();
        
        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id', 'type')
                ->where ('status','=',1)
                ->orderBy('type', 'ASC')
                ->get();

        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();
        
        //Selected job category
        $job_category_map =  $EloquentDb::table('chk_job_category_map') 
                        ->select('category_id') 
                        ->where('job_id', $id)                        
                        ->get();
        
        foreach($job_category_map as $val)
        {
            $selected_categories_id[] = $val["category_id"];
        }
        
        //Selected job industry
        $job_industry_map =  $EloquentDb::table('chk_job_industry_map') 
                        ->select('industry_id') 
                        ->where('job_id', $id)                        
                        ->get();
        
        foreach($job_industry_map as $val)
        {
            $selected_industry_id[] = $val["industry_id"];
        }
        
        //Selected job category
        $job_circular_type =  $EloquentDb::table('chk_job_circular_type') 
                        ->select('job_circular_id') 
                        ->where('job_id', $id)                        
                        ->get();
        
        foreach($job_circular_type as $val)
        {
            $selected_job_circular[] = $val["job_circular_id"];
        }

        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();
            
            $post = array_merge_recursive(
                            $request->getPost()->toArray(), $request->getFiles()->toArray()
                    );
            
           
            if ($post['image-file']['name'] != "") {
                $filename  = stripslashes($post['image-file']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                    $error_messages[]    = "Unknown image extension. <br>
                                            You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                }
            }
            
            if($request->getPost('spot_light_used') == 1)
            {  
               if ($post['spotlight_job_logo']['name'] != "") {
                    $spot_filename  = stripslashes($post['spotlight_job_logo']['name']);
                    $spot_extension = $this->getExtension($spot_filename);
                    $spot_extension = strtolower($spot_extension);
                                      
                    if (($spot_extension != "jpg") && ($spot_extension != "jpeg") && ($spot_extension != "png") && ($spot_extension != "gif") && ($spot_extension != "bmp")) {
                        $error_messages[]    = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }
                
                if($request->getPost('spotlight_job_content') == "")
                {
                    $error_messages[]    = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_order') == "")
                {
                    $error_messages[]    = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_gorup') == "")
                {
                    $error_messages[]    = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }

            // create job form to validation 
            $newspaperJobForm = new \Admin\Form\NewspaperJobForm();
            $newspaperJobForm->inputFilter->setData($request->getPost()->toArray());

            if (($is_validation_error == 0) && ($newspaperJobForm->inputFilter->isValid())) {

                $jobValue = $job->find($id);
                
                //Login user
                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                
                $jobValue->modified_by      = $auth_users->id;
                $jobValue->updated_at       = date("Y-m-d H:i:s");
                $jobValue->job_company_name = $request->getPost('job_company_name');
                $jobValue->company_description = $request->getPost('company_description');
                $jobValue->job_title = $request->getPost('job_title');
                
                $jobValue->job_level           = $request->getPost('job_level');
                $jobValue->job_type            = $request->getPost('job_type');
                //$jobValue->job_level = implode(",", $request->getPost('job_level'));
                //$jobValue->job_type  = implode(",", $request->getPost('job_type'));

                $jobValue->job_location = $request->getPost('job_location');
                $jobValue->no_of_vacencies = $request->getPost('no_of_vacencies');
                $jobValue->job_requirement = $request->getPost('job_requirement');
                $jobValue->job_description = $request->getPost('job_description');
                $jobValue->job_education = $request->getPost('job_education');
                $jobValue->job_experienced = $request->getPost('job_experienced');
                //$jobValue->age_limit_from = $request->getPost('age_limit_from');
                $jobValue->salary_range = $request->getPost('salary_range');
                $jobValue->direct_url = $request->getPost('direct_url');
                $jobValue->job_apply_instruction = $request->getPost('job_apply_instruction');
                $jobValue->compnay_address = $request->getPost('compnay_address');
                $jobValue->other_information = $request->getPost('other_information');
                $jobValue->published = $request->getPost('published');
                $jobValue->posting_date = $request->getPost('posting_date');
                $jobValue->job_deadline = $request->getPost('job_deadline');
                $jobValue->spot_light_used     = $request->getPost('spot_light_used'); 
                $jobValue->job_circular_type     =2;                                
                
                /*
                if ($request->getPost('govtJobStatus') == 1) {
                    $govtJobStatus = "1,2";
                } else {
                    $govtJobStatus = "1";
                }

                $jobValue->job_circular_type = $govtJobStatus;                
                $jobValue->walk_in_active = $request->getPost('walk_in_active');
                */
                
                //Age limit
                if($_POST["type_age"] == "type_age")
                {     
                    $jobValue->age_limit_other = $request->getPost('age_limit_other');
                    $jobValue->age_limit_to    = "";
                    $jobValue->age_limit_from  = "";                
                }
                else
                {
                   $jobValue->age_limit_other = "";
                   $jobValue->age_limit_to    = $request->getPost('age_limit_to');
                   $jobValue->age_limit_from  = $request->getPost('age_limit_from');
                }
                
                
                $jobValue->status = $request->getPost('status');
                
                
                //govt job image
                if ($post['image-file']['name'] != "") {                  

                    $image_name = time().rand(100, 110). '.' . $extension;
                    $newname    = "public/upload/job/" . $image_name;
                    $copied     = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);
                    
                    //delete previous job image
                    unlink("public/upload/job/".$job_images["job_image"]);
                    
                    $jobValue->job_image  = $image_name;
                }
                
                 //spotlight
                if($request->getPost('spot_light_used') == 1)
                {                                
                    if ($post['spotlight_job_logo']['name'] != "")
                    {    
                        $spot_image_name = time() . '.' . $spot_extension;
                        $spot_newname    = "public/upload/job/" . $spot_image_name;
                        $copied          = copy($post['spotlight_job_logo']['name'], $spot_newname);

                        $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);

                        // save image
                        $img->save($spot_newname);
                        
                        //delete previous job image
                        unlink("public/upload/job/".$job_images["spotlight_job_logo"]);
                        
                        $jobValue->spotlight_job_logo    = $spot_image_name;
                    }    
                   
                    $jobValue->spotlight_job_content = $request->getPost('spotlight_job_content');                    
                    $jobValue->spotlight_job_order   = $request->getPost('spotlight_job_order');
                    $jobValue->spotlight_job_gorup   = $request->getPost('spotlight_job_gorup');
                }
                else
                {
                    $jobValue->spotlight_job_content = "";
                    $jobValue->spotlight_job_logo    = "";
                    $jobValue->spotlight_job_order   = "";
                    $jobValue->spotlight_job_gorup   = "";
                }
                

                $jobValue->save();
                
                //Update chk_job_industry_map
                $EloquentDb::table('chk_job_industry_map')->where('job_id', $id)->delete();

                foreach($request->getPost('job_industry_id') as $industry_id)
                {                                
                    $EloquentDb::table('chk_job_industry_map')->insert(
                                        [
                                            'job_id'      => $id, 
                                            'industry_id' => $industry_id
                                        ]
                                    );
                }
                
                //Update job_category_map
                $EloquentDb::table('chk_job_category_map')->where('job_id', $id)->delete();

                foreach($request->getPost('job_category_id') as $category_id)
                {                                
                    $EloquentDb::table('chk_job_category_map')->insert(
                                        [
                                            'job_id'      => $id, 
                                            'category_id' => $category_id
                                        ]
                                    );
                }
                
                //update job circular add
                $EloquentDb::table('chk_job_circular_type')->where('job_id', $id)->delete();
                
                foreach($request->getPost('job_circular_id') as $job_circular_id)
                {
                   $EloquentDb::table('chk_job_circular_type')->insert(
                                        [
                                            'job_id'          => $id, 
                                            'job_circular_id' => $job_circular_id
                                        ]
                                    );
                }
                
                $this->flashMessenger()->setNamespace('success')->addMessage('Update successfull.');
                return $this->redirect()->toUrl("/admin/job/newsjob");    
                
            } else {

                $messages = $this->getErrorMessages($newspaperJobForm->inputFilter);
                if (count($messages) > 0) {
                    $error_messages[] = $messages;
                }
            }
        } else {

            $results = $job->find($id);
        }

        /*
          echo "<pre>";
          print_r($results);
          exit;
         * 
         */


        $view = new ViewModel(array(
            'results' => $results,
            'error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'post_data' => $post_data,
            'job_industry' => $job_industry,
            'job_category' => $job_category,
            'job_circular' => $job_circular,
            'selected_job_circular'  => $selected_job_circular,
            'selected_categories_id' => $selected_categories_id,
            'selected_industry_id'   => $selected_industry_id,
            'header' => 'Edit Newspaper Job'
        ));

        $view->setTemplate('admin/job/newsjobadd.phtml');
        return $view;
    }

    public function editjobAction() {

        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');

        $job = new \Admin\Model\Job();


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();

        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();

            // create job form to validation 
            $newspaperJobForm = new \Admin\Form\NewspaperJobForm();
            $newspaperJobForm->inputFilter->setData($request->getPost()->toArray());

            if ($newspaperJobForm->inputFilter->isValid()) {

                $jobValue = $job->find($id);

                $jobValue->job_company_name = $request->getPost('job_company_name');
                $jobValue->company_description = $request->getPost('company_description');
                $jobValue->job_title = $request->getPost('job_title');
                $jobValue->job_category_id = implode(",", $request->getPost('job_category_id'));
                $jobValue->job_level = implode(",", $request->getPost('job_level'));
                $jobValue->job_type = implode(",", $request->getPost('job_type'));

                $jobValue->job_location = $request->getPost('job_location');
                $jobValue->no_of_vacencies = $request->getPost('no_of_vacencies');
                $jobValue->job_requirement = $request->getPost('job_requirement');
                $jobValue->job_description = $request->getPost('job_description');
                $jobValue->job_education = $request->getPost('job_education');
                $jobValue->job_experienced = $request->getPost('job_experienced');
                $jobValue->age_limit_from = $request->getPost('age_limit_from');
                $jobValue->salary_range = $request->getPost('salary_range');
                $jobValue->direct_url = $request->getPost('direct_url');
                $jobValue->job_apply_instruction = $request->getPost('job_apply_instruction');
                $jobValue->compnay_address = $request->getPost('compnay_address');
                $jobValue->other_information = $request->getPost('other_information');
                $jobValue->published = $request->getPost('published');
                $jobValue->posting_date = $request->getPost('posting_date');
                $jobValue->job_deadline = $request->getPost('job_deadline');
                $jobValue->job_circular_type =2;
                /*
                            if ($request->getPost('govtJobStatus') == 1) {
                                $govtJobStatus = "1,2";
                            } else {
                                $govtJobStatus = "1";
                            }

                            $jobValue->job_circular_type = $govtJobStatus;

                            $jobValue->walk_in_active = $request->getPost('walk_in_active');
                            */
                
                $jobValue->status = $request->getPost('status');

                $jobValue->save();
                
                $this->flashMessenger()->setNamespace('success')->addMessage('Update successfull.');
                return $this->redirect()->toUrl("admin/job/newsjob");       
                    
            } else {

                $messages = $this->getErrorMessages($newspaperJobForm->inputFilter);
                if (count($messages) > 0) {
                    $error_messages[] = $messages;
                }
            }
        } else {

            $results = $job->find($id);
        }

        $view = new ViewModel(array(
            'results' => $results,
            'error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'post_data' => $post_data,
            'job_industry' => $job_industry,
            'job_category' => $job_category
        ));

        $view->setTemplate('admin/job/editjob.phtml');
        return $view;
    }

    public function govtJobAddAction() {
        
        $is_validation_error    = 0;
        $selected_industry_id   = array();
        $selected_categories_id = array();
        $post_data              = array();
        $error_messages         = array();
        $success_messages       = array();
        $request                = $this->getRequest();
        $is_validation_error    = 0;
        $selected_industry_id   = array();
        $selected_categories_id = array();
        
        $job = new \Admin\Model\Job();        
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();

        
        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();
            
            //For image upload
            $post = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );

            // create job form to validation 
            $jobForm = new \Admin\Form\JobForm();
            $jobForm->inputFilter->setData($request->getPost()->toArray());
            
            
            if ($post['image-file']['name'] != ""){

               $filename  = stripslashes($post['image-file']['name']);
               $extension = $this->getExtension($filename);
               $extension = strtolower($extension);

               if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                   $error_messages[]    = "Unknown image extension. <br>
                                           You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                   $is_validation_error = 1;
               }

            }
                        
             //For spot light
            if($request->getPost('spot_light_used') == 1)
            {                
                if ($post['spotlight_job_logo']['name'] == "") {
                    $error_messages[]    = "You must upload an image file for spotlight job with one<br>
                                           of the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                }
                else
                {
                    $spot_filename  = stripslashes($post['spotlight_job_logo']['name']);
                    $spot_extension = $this->getExtension($spot_filename);
                    $spot_extension = strtolower($spot_extension);

                    if (($spot_extension != "jpg") && ($spot_extension != "jpeg") && ($spot_extension != "png") && ($spot_extension != "gif") && ($spot_extension != "bmp")) {
                        $error_messages[]    = "Spot light images unknown extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }
                
                if($request->getPost('spotlight_job_content') == "")
                {
                    $error_messages[]    = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_order') == "")
                {
                    $error_messages[]    = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_gorup') == "")
                {
                    $error_messages[]    = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }

            if (($is_validation_error == 0) && ($jobForm->inputFilter->isValid())) {

                if ($post['image-file']['name'] != "") {                  

                    $image_name = time().  rand(100, 110) . '.' . $extension;
                    $newname = "public/upload/job/" . $image_name;
                    $copied = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);                    
                }       
               

                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                
                $job->created_by        = $auth_users->id;
                $job->created_at        = date("Y-m-d H:i:s");
                $job->job_company_name  = $request->getPost('job_company_name');
                $job->job_title         = $request->getPost('position');
                $job->job_location      = $request->getPost('office_name');
                $job->job_level         = $request->getPost('position');
                $job->job_education     = $request->getPost('job_education');                
                $job->published         = $request->getPost('published');
                $job->posting_date      = $request->getPost('published_date');
                $job->job_deadline      = $request->getPost('job_deadline');
                $job->job_image         = $image_name;
                $job->spot_light_used   = $request->getPost('spot_light_used');
                $job->status            = $request->getPost('status');
                $job->job_circular_type = 2;
                
                //Spotlight
                if($request->getPost('spot_light_used') == 1)
                {
                    $spot_image_name = time() . '.' . $spot_extension;
                    $spot_newname    = "public/upload/job/" . $spot_image_name;
                    $copied          = copy($post['spotlight_job_logo']['name'], $spot_newname);

                    $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);
                    $img->resize(150, 150);

                    // save image
                    $img->save($spot_newname);      
                   
                    $job->spotlight_job_content = $request->getPost('spotlight_job_content');
                    $job->spotlight_job_logo    = $spot_image_name;
                    $job->spotlight_job_order   = $request->getPost('spotlight_job_order');
                    $job->spotlight_job_gorup   = $request->getPost('spotlight_job_gorup');
                }
                
                $job->save();
                $job_id = $job->id;
                
                $this->updateSpotlightJobId($job_id);
                
                //Add job industry
                foreach($request->getPost('job_industry_id') as $industry_id)
                {
                   $EloquentDb::table('chk_job_industry_map')->insert(
                                        [
                                            'job_id'      => $job_id, 
                                            'industry_id' => $industry_id
                                        ]
                                    );
                }
                
                //Add job category
                foreach($request->getPost('job_category_id') as $category_id)
                {
                   $EloquentDb::table('chk_job_category_map')->insert(
                                        [
                                            'job_id'      => $job_id, 
                                            'category_id' => $category_id
                                        ]
                                    );
                }
                
                //job circular add
                $job_circular = $EloquentDb::table('chk_job_circular')
                                ->select('id')
                                ->where('type', '=', 'Govjob')
                                ->first();
                
                $EloquentDb::table('chk_job_circular_type')->insert(
                                     [
                                         'job_id'          => $job_id, 
                                         'job_circular_id' => $job_circular['id']
                                     ]
                                 );
               
                
                
                //Update job counter
                if($request->getPost('status') == "1")
                {    
                    //update chk_job_category
                    $update = new \Admin\Model\General();                
                    $update->inrementJob($EloquentDb, $request->getPost('job_category_id'),'chk_job_category');

                    //update chk_job_industry
                    $update = new \Admin\Model\General();                
                    $update->inrementJob($EloquentDb, $request->getPost('job_industry_id'),'chk_job_industry');
                }
                

                $this->flashMessenger()->setNamespace('success')->addMessage('Govt job has been added.');
                return $this->redirect()->toUrl("/admin/job/govtjob");
                
            } else {

                $messages = $this->getErrorMessages($jobForm->inputFilter);
                if (count($messages) > 0) {
                    $error_messages = $messages;
                }
            }
        }


        return new ViewModel(array(
            'error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'post_data' => $post_data,
            'job_industry' => $job_industry,
            'job_category' => $job_category,
            'selected_industry_id' => $selected_industry_id,
            'selected_categories_id' => $selected_categories_id
        ));
    }

    public function govtJobEditAction() {

        $is_validation_error        = 0;
        $error_messages             = array();
        $post_data                  = array();
        $selected_industry_id       = array();
        $selected_categories_id     = array();
        
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();
        
        //Login user
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Delete previous job images
        $job_images =  $EloquentDb::table('chk_jobs') 
                                        ->select('job_image','spotlight_job_logo') 
                                        ->where('id', $id)                        
                                        ->first();
        
        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();
        
        //Selected job industry
        $job_category_map =  $EloquentDb::table('chk_job_industry_map') 
                        ->select('industry_id') 
                        ->where('job_id', $id)                        
                        ->get();
        
        foreach($job_category_map as $val)
        {
            $selected_industry_id[] = $val["industry_id"];
        }
        
        //Selected job category
        $job_category_map =  $EloquentDb::table('chk_job_category_map') 
                        ->select('category_id') 
                        ->where('job_id', $id)                        
                        ->get();
        
        foreach($job_category_map as $val)
        {
            $selected_categories_id[] = $val["category_id"];
        }
        
        //echo "<pre>";
        //print_r($selected_categories_id);
        //exit;

        if ($request->isPost()) {
            
            // get post data 
            $post_data = $request->getPost();

            
            $jobValue = $job->find($id);
            
            $post = array_merge_recursive(
                            $request->getPost()->toArray(), $request->getFiles()->toArray()
                    );            
            
            if ($post['image-file']['name'] != "") {
                $filename  = stripslashes($post['image-file']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                    $error_messages[]    = "Unknown image extension. <br>
                                            You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                }
            }
            
            if($request->getPost('spot_light_used') == 1)
            {  
               if ($post['spotlight_job_logo']['name'] != "") {
                    $spot_filename  = stripslashes($post['spotlight_job_logo']['name']);
                    $spot_extension = $this->getExtension($spot_filename);
                    $spot_extension = strtolower($spot_extension);

                    if (($spot_extension != "jpg") && ($spot_extension != "jpeg") && ($spot_extension != "png") && ($spot_extension != "gif") && ($spot_extension != "bmp")) {
                        $error_messages[]    = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }
                
                if($request->getPost('spotlight_job_content') == "")
                {
                    $error_messages[]    = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_order') == "")
                {
                    $error_messages[]    = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_gorup') == "")
                {
                    $error_messages[]    = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }
            
            if ($is_validation_error == 0){

                $jobValue->modified_by      = $auth_users->id;
                $jobValue->updated_at       = date("Y-m-d H:i:s");
                $jobValue->job_company_name = $request->getPost('job_company_name');
                $jobValue->job_title        = $request->getPost('position');
                $jobValue->job_location     = $request->getPost('office_name');
                $jobValue->job_level        = $request->getPost('position');
                $jobValue->job_education    = $request->getPost('job_education');                
                $jobValue->published        = $request->getPost('published');
                $jobValue->posting_date     = $request->getPost('published_date');
                $jobValue->job_deadline     = $request->getPost('job_deadline');
                $jobValue->spot_light_used  = $request->getPost('spot_light_used');
                $jobValue->status           = $request->getPost('status');
                $jobValue->job_circular_type =2;
                //govt job image
                if ($post['image-file']['name'] != "") {                  

                    $image_name = time(). rand(100, 110) . '.' . $extension;
                    $newname    = "public/upload/job/" . $image_name;
                    $copied     = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);
                    
                    //delete previous job image
                    unlink("public/upload/job/".$job_images["job_image"]);
                    
                    $jobValue->job_image  = $image_name;
                    
                    
                }    
                
                //spotlight
                if($request->getPost('spot_light_used') == 1)
                {                                
                    if ($post['spotlight_job_logo']['name'] != "")
                    {    
                        $spot_image_name = time() . '.' . $spot_extension;
                        $spot_newname    = "public/upload/job/" . $spot_image_name;
                        $copied          = copy($post['spotlight_job_logo']['name'], $spot_newname);

                        $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);

                        // save image
                        $img->save($spot_newname);
                        $img->resize(150, 150);
                        
                        //delete previous job image
                        unlink("public/upload/job/".$job_images["spotlight_job_logo"]);
                        
                        $jobValue->spotlight_job_logo    = $spot_image_name;
                    }    
                   
                    $jobValue->spotlight_job_content = $request->getPost('spotlight_job_content');                    
                    $jobValue->spotlight_job_order   = $request->getPost('spotlight_job_order');
                    $jobValue->spotlight_job_gorup   = $request->getPost('spotlight_job_gorup');
                }
                else
                {
                    $jobValue->spotlight_job_content = "";
                    $jobValue->spotlight_job_logo    = "";
                    $jobValue->spotlight_job_order   = "";
                    $jobValue->spotlight_job_gorup   = "";
                }                
                
                $jobValue->save();
            
                //Update job_industry_map
                $EloquentDb::table('chk_job_industry_map')->where('job_id', $id)->delete();

                foreach($request->getPost('job_industry_id') as $industry_id)
                {                                
                    $EloquentDb::table('chk_job_industry_map')->insert(
                                        [
                                            'job_id'      => $id, 
                                            'industry_id' => $industry_id
                                        ]
                                    );
                }

                //Update job_category_map
                $EloquentDb::table('chk_job_category_map')->where('job_id', $id)->delete();

                foreach($request->getPost('job_category_id') as $category_id)
                {                                
                    $EloquentDb::table('chk_job_category_map')->insert(
                                        [
                                            'job_id'      => $id, 
                                            'category_id' => $category_id
                                        ]
                                    );
                }
                
                /*
                if($request->getPost('status') == "0")
                {    
                    //update chk_job_category
                    $update = new \Admin\Model\General();                
                    $update->decrementJob($EloquentDb, $request->getPost('job_category_id'),'chk_job_category');

                    //update chk_job_industry
                    $update = new \Admin\Model\General();                
                    $update->decrementJob($EloquentDb, $request->getPost('job_industry_id'),'chk_job_industry');
                }
                **/


                $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
                return $this->redirect()->toUrl("/admin/job/govtjob");
            }
                
        } else {

            $results = $job->find($id);
        }


        $view = new ViewModel(array(
            'results'                => $results,
            'error_messages'         => $error_messages,
            'post_data'              => $post_data,
            'job_industry'           => $job_industry,
            'job_category'           => $job_category,
            'selected_industry_id'   => $selected_industry_id,
            'selected_categories_id' => $selected_categories_id
        ));

        $view->setTemplate('admin/job/govtjobadd.phtml');
        return $view;
    }

    public function getErrorMessages($formInpuFilter) {
        $error_messages = array();

        foreach ($formInpuFilter->getInvalidInput() as $messageId => $error) {
            $err = $error->getMessages();

            foreach ($err as $er) {

                $error_messages[] = $messageId . " : " . $er;
            }
        }

        return $error_messages;
    }

    function editAction() {

        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();

        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();

            // create job form to validation 
            $newspaperJobForm = new \Admin\Form\NewspaperJobForm();
            $newspaperJobForm->inputFilter->setData($request->getPost()->toArray());

            if ($newspaperJobForm->inputFilter->isValid()) {

                $jobValue = $job->find($id);

                $jobValue->company_name = $request->getPost('company_name');
                $jobValue->company_description = $request->getPost('company_description');
                $jobValue->job_title = $request->getPost('job_title');
                $jobValue->job_category_id = implode(",", $request->getPost('job_category_id'));
                $jobValue->job_level = implode(",", $request->getPost('job_level'));
                $jobValue->job_type = implode(",", $request->getPost('job_type'));

                $jobValue->job_location = $request->getPost('job_location');
                $jobValue->no_of_vacencies = $request->getPost('no_of_vacencies');
                $jobValue->job_requirement = $request->getPost('job_requirement');
                $jobValue->job_description = $request->getPost('job_description');
                $jobValue->job_education = $request->getPost('job_education');
                $jobValue->job_experienced = $request->getPost('job_experienced');
                $jobValue->age_limit_from = $request->getPost('age_limit_from');
                $jobValue->salary_range = $request->getPost('salary_range');
                $jobValue->direct_url = $request->getPost('direct_url');
                $jobValue->job_apply_instruction = $request->getPost('job_apply_instruction');
                $jobValue->compnay_address = $request->getPost('compnay_address');
                $jobValue->other_information = $request->getPost('other_information');
                $jobValue->published = $request->getPost('published');
                $jobValue->posting_date = $request->getPost('posting_date');
                $jobValue->job_deadline = $request->getPost('job_deadline');

                if ($request->getPost('govtJobStatus') == 1) {
                   // $govtJobStatus = "1,2";
                     $govtJobStatus = "2";
                } else {
                    $govtJobStatus = "1";
                }

                $jobValue->job_circular_type = $govtJobStatus;
                

                $jobValue->walk_in_active = $request->getPost('walk_in_active');
                $jobValue->status = $request->getPost('status');

                $jobValue->save();
            } else {

                $messages = $this->getErrorMessages($newspaperJobForm->inputFilter);
                if (count($messages) > 0) {
                    $error_messages[] = $messages;
                }
            }
        } else {

            $results = $job->find($id);
        }

        /*
          echo "<pre>";
          print_r($results);
          exit;
         * 
         */


        $view = new ViewModel(array(
            'results' => $results,
            'error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'post_data' => $post_data,
            'job_industry' => $job_industry,
            'job_category' => $job_category
        ));

        $view->setTemplate('admin/job/editjob.phtml');
        return $view;
    }

    public function archiveAction() {

        return new ViewModel();
    }

    public function walkinAction() {

        return new ViewModel();
    }

    public function walkinaddAction() {

        $is_validation_error = 0;
        $error_messages = array();
        $post_data = array();
        $results = array();
        $request = $this->getRequest();
        $job = new \Admin\Model\Job();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();


        if ($request->isPost()) {

            // get post data 
            $post_data = $request->getPost();

            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );

            if ($post['image-file']['name'] != "") {
                $filename = stripslashes($post['image-file']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);

                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
                    $error_messages[] = "Unknown image extension.";
                    $is_validation_error = 1;
                } else {

                    $image_name = time() . '.' . $extension;
                    $newname = "public/upload/job/" . $image_name;
                    $copied = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);
                }
            }

            if ($is_validation_error == 0) {

                $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                
                $job->created_by        = $auth_users->id;
                $job->created_at        = date("Y-m-d H:i:s");
                $job->job_company_name  = $request->getPost('job_company_name');
                $job->job_level         = $request->getPost('position');
                $job->job_education     = $request->getPost('job_education');
                $job->job_category_id   = implode(",", $request->getPost('job_category_id'));
                $job->published         = $request->getPost('published');
                $job->posting_date      = $request->getPost('published_date');
                $job->job_deadline      = $request->getPost('job_deadline');
                $job->job_image         = $image_name;
                $job->status            = $request->getPost('status');
                $job->job_circular_type = 2;

                $job->save();
                $job_id = $job->id;
                
                $this->updateSpotlightJobId($job_id);
                
                //job circular add
                $job_circular = $EloquentDb::table('chk_job_circular')
                                ->select('id')
                                ->where('type', '=', 'Walk in interview')
                                ->first();
                
                $EloquentDb::table('chk_job_circular_type')->insert(
                                     [
                                         'job_id'          => $job_id, 
                                         'job_circular_id' => $job_circular['id']
                                     ]
                                 );

                $this->flashMessenger()->setNamespace('success')->addMessage('Walk in job has been added.');
                return $this->redirect()->toUrl("/admin/job/walkin");
            }
        }

        return new ViewModel(array(
            'post_data' => $post_data,
            'results' => $results,
            'error_messages' => $error_messages,
            'job_category' => $job_category
        ));
    }

    public function editwalkinAction() {

        $is_validation_error = 0;
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();
        
        //Login user
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        //Delete previous job images
        $job_images =  $EloquentDb::table('chk_jobs') 
                                        ->select('job_image','spotlight_job_logo') 
                                        ->where('id', $id)                        
                                        ->first();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->get();

        if ($request->isPost()) {

            $jobValue = $job->find($id);
            
             // get post data 
            $post_data = $request->getPost();

            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );

            if ($post['image-file']['name'] != "") {
                $filename = stripslashes($post['image-file']['name']);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);

                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
                    $error_messages[] = "Unknown image extension.";
                    $is_validation_error = 1;                } 
            }
            
            if ($is_validation_error == 0) {
             
                $jobValue->modified_by      = $auth_users->id;
                $jobValue->updated_at       = date("Y-m-d H:i:s");
                $jobValue->job_company_name = $request->getPost('job_company_name');
                $jobValue->job_level        = $request->getPost('position');
                $jobValue->job_education    = $request->getPost('job_education');
                $jobValue->job_category_id  = implode(",", $request->getPost('job_category_id'));
                $jobValue->published        = $request->getPost('published');
                $jobValue->posting_date     = $request->getPost('published_date');
                $jobValue->job_deadline     = $request->getPost('job_deadline');
                $jobValue->status           = $request->getPost('status');
                $jobValue->job_circular_type           =2;
                //job image
                if ($post['image-file']['name'] != "") {                  

                    $image_name = time().rand(100, 110). '.' . $extension;
                    $newname    = "public/upload/job/" . $image_name;
                    $copied     = copy($post['image-file']['name'], $newname);

                    $img = Image::make($post['image-file']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);

                    // save image
                    $img->save($newname);
                    
                    //delete previous job image
                    unlink("public/upload/job/".$job_images["job_image"]);
                    
                    $jobValue->job_image  = $image_name;
                }

                $jobValue->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Update SuccessFul');
                return $this->redirect()->toUrl("/admin/job/walkin");
            }    
        } else {

            $results = $job->find($id);
        }

        $view = new ViewModel(array(
            'post_data' => $post_data,
            'results' => $results,
            'job_category' => $job_category
        ));

        $view->setTemplate('admin/job/walkinadd.phtml');
        return $view;
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

    public function deletewalkinAction() {

        $id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();

        $jobVal = $job->find($id);
        $jobVal->trashed = 1;
        $jobVal->save();

        $this->flashMessenger()->setNamespace('success')->addMessage('Walk in interview job has been deleted.');
        return $this->redirect()->toUrl("/admin/job/walkin");
    }

    /*
     * 
     * /
     * 
     */

    public function spotlightaddAction() {
        $post_data = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post_data = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
 
            if ($post_data['image-file']['name'] != "") {
                //$destination = dirname(__DIR__).'/public/images/job/';
                $destination = 'images/';
                $filename = str_random(32) . '.' . $post_data['image-file']['name'];
                $new_filename = $destinationPath . '' . str_random(32) . '.' . $post_data['image-file']['name'];
                // read image from temporary file
              //  $img = Image::make($post_data['image-file']['tmp_name']);

                // resize image
                //$img->fit(300, 200);

                // save image
                //$img->save($destination . $filename);
            }

            $spotlight = new \Admin\Model\Spotlightjob();
            $spotlight->job_title = $post_data['job_title'];
            $spotlight->job_content = $post_data['job_content'];
            $spotlight->job_url = $post_data['job_url'];
            $spotlight->job_deadline = $post_data['job_deadline'];
            $spotlight->status = $post_data['status'];
            $spotlight->job_order = $post_data['job_order'];
            $spotlight->job_logo = $new_filename;
            $spotlight->save();


            /* $form->setData($post);
              if ($form->isValid()) {
              $data = $form->getData();
              // Form is valid, save the form!
              return $this->redirect()->toRoute('upload-form/success');
              } */
        }

        $view = new ViewModel(array(
            'post_data' => $post_data,
            'results' => $results,
            'job_category' => $job_category
        ));


        $view->setTemplate('admin/job/spotlightform.phtml');
        return $view;
    }
    
    
    /*
     * 
     * 
     * /
     
     */
    
    public function spotlighteditAction() {
        $id = $this->params()->fromRoute('id');
       $results=  \Admin\Model\Spotlightjob::where('id','=',$id)->get();
       
        $post_data = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post_data = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );
 
            if ($post_data['image-file']['name'] != "") {
                //$destination = dirname(__DIR__).'/public/images/job/';
                $destination = 'images/';
                $filename = str_random(32) . '.' . $post_data['image-file']['name'];
                $new_filename = $destinationPath . '' . str_random(32) . '.' . $post_data['image-file']['name'];
                // read image from temporary file
              //  $img = Image::make($post_data['image-file']['tmp_name']);

                // resize image
                //$img->fit(300, 200);

                // save image
                //$img->save($destination . $filename);
            }

            $spotlight = new \Admin\Model\Spotlightjob();
            $spotlight->job_title = $post_data['job_title'];
            $spotlight->job_content = $post_data['job_content'];
            $spotlight->job_url = $post_data['job_url'];
            $spotlight->job_deadline = $post_data['job_deadline'];
            $spotlight->status = $post_data['status'];
            $spotlight->job_order = $post_data['job_order'];
            $spotlight->job_logo = $new_filename;
            $spotlight->save();


            /* $form->setData($post);
              if ($form->isValid()) {
              $data = $form->getData();
              // Form is valid, save the form!
              return $this->redirect()->toRoute('upload-form/success');
              } */
        }

        $view = new ViewModel(array(
            'post_data' => $post_data,
            'results' => $results,
            'job_category' => $job_category
        ));


        $view->setTemplate('admin/job/spotlightform.phtml');
        return $view;
    }
    
    public function repostoldAction() {
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        $job     = new \Admin\Model\Job();
        
        if ($request->isPost()) {
            
            $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
            
            $EloquentDb::table('chk_jobs')
            ->where('id', $id)
            ->update(array(
                            'repost_date'  => date("Y-m-d"),                            
                            'is_repost'    =>  $request->getPost('is_repost')
                          ));
            
           $EloquentDb::table('chk_jobs')->where('id', $id)->increment('repost_count'); 
            
           $this->flashMessenger()->setNamespace('success')->addMessage('Job has been reposted.');
           return $this->redirect()->toUrl("/admin/job/archive"); 
            
        }else {

            $results = $job->find($id);
        }
        
        
        $view = new ViewModel(array(
            'post_data' => $post_data,
            'results' => $results            
        ));
        
        
        $view->setTemplate('admin/job/repost.phtml');
        return $view;
    }
    
    public function repostAction ()
    {   
        $is_validation_error        = 0;
        $error_messages             = array();
        $post_data                  = array();
        $selected_categories_id     = array();
        $selected_industry_id       = array();
        $selected_preferred_role_id = array();
        $selected_business_type_id  = array();
        $selected_division_id       = array();
        $selected_country_id        = array();
          
                        
        $request = $this->getRequest();
        //$id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();
        
        //$company_id = $this->params()->fromRoute('id');
        $job_id     = $this->params()->fromRoute('id');
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $employer = $EloquentDb::table('chk_jobs')
                ->select('employer_id')
                ->where('id', $job_id)
                ->first();
        
        $employer_id = $employer["employer_id"];
                     
               
        //For Login user
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        
        $division = $EloquentDb::table('chk_division')
                ->select('id','name')                
                ->get();
        
        $country = $EloquentDb::table('chk_country')
                ->select('id','name')
                ->where('status', 1)   
                ->get();
        
        //Job category
        $job_category =  $EloquentDb::table('chk_job_category') 
        ->select('id', 'name') 
        ->orderBy('name', 'ASC')        
        ->get();
        
         //Job category
        $job_industry =  $EloquentDb::table('chk_job_industry') 
        ->select('id', 'name') 
        ->orderBy('name', 'ASC')        
        ->get();
        
        //Business type
        $business_type =  $EloquentDb::table('chk_business_type') 
        ->select('id', 'name') 
        ->where('status', 1)        
        ->get();
        
        //Department
        $department =  $EloquentDb::table('chk_department') 
        ->select('id', 'name') 
        ->where('status', 1)
        ->orderBy('name', 'ASC')        
        ->get();
        
        foreach($department as $val)
        {
           //Department
            $role =  $EloquentDb::table('chk_preferred_role') 
            ->select('id', 'name') 
            ->where('department_id', $val["id"]) 
            ->where('status', 1)
            ->orderBy('name', 'ASC')        
            ->get();
            
            $preferred_role[$val["id"]]= $role;            
        }
        
        //Selected devision
        $job_division =  $EloquentDb::table('chk_job_division') 
                        ->select('division_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($job_division as $val)
        {
            $selected_division_id[] = $val["division_id"];
        }        
                        
        //Selected country
        $job_country =  $EloquentDb::table('chk_job_country') 
                        ->select('country_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($job_country as $val)
        {
            $selected_country_id[] = $val["country_id"];
        }        
                               
        //Selected preferred role
        $job_role =  $EloquentDb::table('chk_job_role') 
                        ->select('preferred_role_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($job_role as $val)
        {
            $selected_preferred_role_id[] = $val["preferred_role_id"];
        }
        
        //Selected preferred role
        $job_business =  $EloquentDb::table('chk_job_business') 
                        ->select('business_type_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($job_business as $val)
        {
            $selected_business_type_id[] = $val["business_type_id"];
        }
        
        
        //Selected job category
        $job_category_map =  $EloquentDb::table('chk_job_category_map') 
                        ->select('category_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($job_category_map as $val)
        {
            $selected_categories_id[] = $val["category_id"];
        }
        
        //Selected job industry
        $chk_job_industry_map =  $EloquentDb::table('chk_job_industry_map') 
                        ->select('industry_id') 
                        ->where('job_id', $job_id)                        
                        ->get();
        
        foreach($chk_job_industry_map as $val)
        {
            $selected_industry_id[] = $val["industry_id"];
        }
        
                
        if ($request->isPost()) {
            // get post data 
            $post_data = $request->getPost();
            
            $jobValue = $job;
            
                        
            if($request->getPost('spot_light_used') == 1)
            {
               $post = array_merge_recursive(
                            $request->getPost()->toArray(), $request->getFiles()->toArray()
                    );
               
               if ($post['spotlight_job_logo']['name'] != "") {
                    $filename  = stripslashes($post['spotlight_job_logo']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);

                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[]    = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }
                
                if($request->getPost('spotlight_job_content') == "")
                {
                    $error_messages[]    = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_order') == "")
                {
                    $error_messages[]    = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }
                
                if($request->getPost('spotlight_job_gorup') == "")
                {
                    $error_messages[]    = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }    
            
            if ($is_validation_error == 0){
            
                $jobValue->employer_id         = $employer_id;
                $jobValue->modified_by         = $auth_users->id;
                $jobValue->updated_at          = date("Y-m-d H:i:s");

                $jobValue->job_title           = $request->getPost('job_title');
                $jobValue->no_of_vacencies     = $request->getPost('no_of_vacencies');
                $jobValue->job_location        = $request->getPost('job_location');

                if($request->getPost('job_location') == "Other")
                {
                    $jobValue->other_location  = $request->getPost('other_location'); 
                }

                $jobValue->age_limit_from      = $request->getPost('age_limit_from');
                $jobValue->job_level           = $request->getPost('job_level');
                $jobValue->job_type            = $request->getPost('job_type');
                $jobValue->posting_date        = $request->getPost('posting_date');
                $jobValue->job_deadline        = $request->getPost('job_deadline');
                $jobValue->job_education       = $request->getPost('job_education');
                $jobValue->salary_range        = $request->getPost('salary_range');
                $jobValue->other_benefits      = $request->getPost('other_benefits');
                $jobValue->job_experienced     = $request->getPost('job_experienced');               
                $jobValue->job_requirement     = $request->getPost('job_requirement');
                $jobValue->job_description     = $request->getPost('job_description');               
                $jobValue->spot_light_used     = $request->getPost('spot_light_used');            
                $jobValue->show_company_status = $request->getPost('show_company_status');
                $jobValue->status              = $request->getPost('status');
                $jobValue->is_repost           = 1;
                
                //For employee job
                $jobValue->job_circular_type   = 1;

                $jobValue->online_cv_received = $request->getPost('online_cv_received');

                if($_POST["email_cv"] == "EmailCV")
                {     
                    $jobValue->email_cv_received  = $request->getPost('email_cv_received');
                }
                else
                {
                   $jobValue->email_cv_received = "";
                }    

                if($_POST["hardcopy"] == "hardcopy")
                {     
                    $jobValue->hardcopy_cv_received  = $request->getPost('hardcopy_cv_received');
                }
                else
                {
                   $jobValue->hardcopy_cv_received = "";
                }
                
                //spotlight
                if($request->getPost('spot_light_used') == 1)
                {                                
                    if ($post['spotlight_job_logo']['name'] != "")
                    {    
                        $image_name = time() . '.' . $extension;
                        $newname    = "public/images/job/" . $image_name;
                        $copied     = copy($post['spotlight_job_logo']['name'], $newname);

                        $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);

                        // save image
                        $img->save($newname);
                        
                        $jobValue->spotlight_job_logo    = $image_name;
                    }    
                   
                    $jobValue->spotlight_job_content = $request->getPost('spotlight_job_content');                    
                    $jobValue->spotlight_job_order   = $request->getPost('spotlight_job_order');
                    $jobValue->spotlight_job_gorup   = $request->getPost('spotlight_job_gorup');
                }
                else
                {
                    $jobValue->spotlight_job_content = "";
                    $jobValue->spotlight_job_logo    = "";
                    $jobValue->spotlight_job_order   = "";
                    $jobValue->spotlight_job_gorup   = "";
                }    
            
            
                $jobValue->save();
                $repost_job_id = $job->id;
                
                if($request->getPost('job_location') == "Within Bangladesh")
                {
                     foreach($request->getPost('division_id') as $division_id)
                     {                     
                         $EloquentDb::table('chk_job_division')->insert(
                                         [
                                             'job_id'      => $repost_job_id, 
                                             'division_id' => $division_id
                                         ]
                                     );
                     }
                }

                if($request->getPost('job_location') == "Outside Bangladesh")
                {
                     foreach($request->getPost('country_id') as $country_id)
                     {
                       $EloquentDb::table('chk_job_country')->insert(
                                       [
                                           'job_id'     => $repost_job_id, 
                                           'country_id' => $country_id
                                       ]
                                   );
                     }
                }

                foreach($request->getPost('job_category_id') as $category_id)
                {
                   $EloquentDb::table('chk_job_category_map')->insert(
                                        [
                                            'job_id'      => $repost_job_id, 
                                            'category_id' => $category_id
                                        ]
                                    );
                }
                
                foreach($request->getPost('job_industry_id') as $industry_id)
                {
                   $EloquentDb::table('chk_job_industry_map')->insert(
                                        [
                                            'job_id'      => $repost_job_id, 
                                            'industry_id' => $industry_id
                                        ]
                                    );
                }

                foreach($request->getPost('preferred_role_id') as $preferred_role_id)
                {
                   $EloquentDb::table('chk_job_role')->insert(
                                        [
                                            'job_id'            => $repost_job_id, 
                                            'preferred_role_id' => $preferred_role_id
                                        ]
                                    );
                }

                foreach($request->getPost('business_type_id') as $business_type_id)
                {
                   $EloquentDb::table('chk_job_business')->insert(
                                        [
                                            'job_id'           => $repost_job_id, 
                                            'business_type_id' => $business_type_id
                                        ]
                                    );
                }
                
                //add job circular
                $EloquentDb::table('chk_job_circular_type')->insert(
                                     [
                                         'job_id'          => $repost_job_id, 
                                         'job_circular_id' => 5
                                     ]
                                 );
                
                //Not used                
                /*
                            if($request->getPost('status') == "0")
                            {    
                                //update chk_job_category
                                $update = new \Admin\Model\General();                
                                $update->decrementJob($EloquentDb, $request->getPost('job_category_id'),'chk_job_category');

                                //update chk_job_industry
                                $update = new \Admin\Model\General();                
                                $update->decrementJob($EloquentDb, $request->getPost('job_industry_id'),'chk_job_industry');
                            }
                             * 
                             */
                
                $this->flashMessenger()->setNamespace('success')->addMessage('Your job has been reposted. You can find your reposted job from active job.');
                return $this->redirect()->toUrl("/admin/job/archive");               
               
            }
            
        } else {
            
            $results = $job->find($job_id);            
        }        
        
                        
        $view = new ViewModel(array(
                                'error_messages'             => $error_messages,
                                'results'                    => $results,                                
                                'post_data'                  => $post_data,
                                'division'                   => $division,
                                'country'                    => $country,
                                'job_category'               => $job_category,
                                'job_industry'               => $job_industry,
                                'business_type'              => $business_type,
                                'department'                 => $department,
                                'preferred_role'             => $preferred_role,
                                'selected_division_id'       => $selected_division_id,
                                'selected_country_id'        => $selected_country_id,
                                'selected_preferred_role_id' => $selected_preferred_role_id,
                                'selected_business_type_id'  => $selected_business_type_id,                                
                                'selected_categories_id'     => $selected_categories_id,
                                'selected_industry_id'       => $selected_industry_id,
                                'company_id'                 => $company_id
                            ));

        //$view->setTemplate('admin/job/repost.phtml');
        //return $view;
        
        $view->setTemplate('admin/company/addjob.phtml');
        return $view;
    }
    
    public function updateSpotlightJobId($job_id)
    {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $EloquentDb::table('chk_jobs')
            ->where('id', $job_id)
            ->update(array('job_id' => $job_id));
        
        return true;
    }
}
