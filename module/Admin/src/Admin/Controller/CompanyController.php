<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

class CompanyController extends AbstractActionController {

    public function indexAction() {
        $total_employer_count = \Chkuser\Model\User::where('user_type', '=', 4)->count();
        return new ViewModel(array('total_employer' => $total_employer_count));
    }

    public function servicesListAction() {

        $request = $this->getRequest();
        $post_data = $request->getPost();
        $user = new \Chkuser\Model\User();

        $id = $this->params()->fromRoute('cid');


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $employer_profile = $EloquentDb::table('chk_employer_profile')
                ->select('membership_service_id', 'membership_start_date', 'membership_expire_date')
                ->where('user_id', $id)
                ->get();

        //print_r($employer_profile);

        $servicesList = $EloquentDb::table('chk_membership_service')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        if ($request->isPost()) {

            $EloquentDb::table('chk_employer_profile')
                    ->where('user_id', $id)
                    ->update(array(
                        'membership_service_id' => implode(",", $request->getPost('membership_service_id')),
                        'membership_start_date' => $request->getPost('membership_start_date'),
                        'membership_expire_date' => $request->getPost('membership_expire_date')
            ));


            $this->flashMessenger()->setNamespace('success')->addMessage('Membership services has been updated.');
            return $this->redirect()->toUrl("/admin/company/serviceslist/$id");
        }

        //$prev_url = $this->getRequest()->getHeader('Referer')->getUri();

        return new ViewModel(array(
            'membership_service' => $membership_service,
            'servicesList' => $servicesList,
            'employer_profile' => $employer_profile
        ));
    }

    public function jobsAction() {

        $company_id = $this->params()->fromRoute('cid');

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $results = $EloquentDb::table('chk_jobs')
                ->select('*')
                ->where('employer_id', $company_id)
                ->orderBy('id', "DESC")
                ->get();

        return new ViewModel(array(
            'jobs' => $results,
            'company_id' => $company_id
        ));
    }

    public function employerEditAction() {

        $is_validation_error = 0;
        $post_data = array();
        $error_messages = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

        $request = $this->getRequest();
        $id = $this->params()->fromRoute('cid');
        $user = new \Chkuser\Model\User();

        //Job category
        $employer_profile = $EloquentDb::table('chk_employer_profile')
                ->select(
                        'is_top_employer', 'sliding_option', 'top_employer_order', 'company_url', 'company_description', 'company_service', 'company_banner', 'company_logo'
                )
                ->where('user_id', $id)
                ->get();

        //Invoice
        $invoice = $EloquentDb::table('chk_invoice')
                ->select('*')
                ->where('comapny_id', '=', $id)
                ->orderBy('id', 'DESC')
                ->get();

        $is_top_employer = $employer_profile[0]['is_top_employer'];

        if ($request->isPost()) {

            $userVal = $user->find($id);

            if ($request->getPost('user_profile') == "Update") {
                $userVal->full_name = $request->getPost('full_name');
                $userVal->email = $request->getPost('email');
                $userVal->user_status = $request->getPost('user_status');
                $userVal->user_role = $request->getPost('user_role');
                $userVal->save();

                //Notification for user role permitted
                if ($request->getPost('user_role') == "permitted") {
                    $EloquentDb::table('chk_notification')->insert(
                            [
                                'from_user_id' => $auth_users->id,
                                'to_user_id' => $id,
                                'subject' => "Your user role has been changed.",
                                'description' => "Your are permitted for new job post.",
                                'form_user_status' => 'read',
                                'to_user_status' => 'unread',
                                'type' => 'user_role',
                                'created_at' => date("Y-m-d H:i:s")
                            ]
                    );
                }

                //update top emploer   
                $EloquentDb::table('chk_employer_profile')
                        ->where('user_id', $id)
                        ->update(array(
                            'is_top_employer' => $request->getPost('is_top_employer'),
                            'sliding_option' => $request->getPost('sliding_option'),
                            'top_employer_order' => $request->getPost('top_employer_order'),
                             'updated_at' => date("Y-m-d H:i:s")
                ));

                $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                return $this->redirect()->toUrl("/admin/company");
            }

            if ($request->getPost('company_information') == "Update") {
                $post = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );



                if ($post['company_banner']['name'] != "") {

                    $filename = stripslashes($post['company_banner']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);

                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[] = "Banner image unknown extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }

                if ($post['company_logo']['name'] != "") {

                    $logo_filename = stripslashes($post['company_logo']['name']);
                    $logo_extension = $this->getExtension($logo_filename);
                    $logo_extension = strtolower($logo_extension);

                    if (($logo_extension != "jpg") && ($logo_extension != "jpeg") && ($logo_extension != "png") && ($logo_extension != "gif") && ($logo_extension != "bmp")) {
                        $error_messages[] = "Company logo unknown extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }

                if ($is_validation_error == 0) {
                    if ($post['company_banner']['name'] != "") {

                        $image_name = time() . rand(100, 110) . '.' . $extension;
                        $newname = "public/upload/client/" . $image_name;
                        $copied = copy($post['company_banner']['name'], $newname);

                        $img = Image::make($post['company_banner']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);
                        // save image
                        $img->save($newname);
                    } else {
                        $image_name = $employer_profile[0]["company_banner"];
                    }

                    if ($post['company_logo']['name'] != "") {

                        $logo_name = time() . rand(100, 110) . '.' . $logo_extension;
                        $logo_newname = "public/upload/client/" . $logo_name;
                        $copied = copy($post['company_logo']['name'], $logo_newname);

                        $img = Image::make($post['company_logo']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);
                        // save image
                        $img->save($logo_newname);
                    } else {
                        $logo_name = $employer_profile[0]["company_logo"];
                    }

                    $EloquentDb::table('chk_employer_profile')
                            ->where('user_id', $id)
                            ->update(array(
                                'company_url' => $request->getPost('company_url'),
                                'company_description' => $request->getPost('company_description'),
                                'company_service' => $request->getPost('company_service'),
                                'company_banner' => $image_name,
                                'company_logo' => $logo_name
                    ));

                    $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                    return $this->redirect()->toUrl("/admin/company");
                }
            }

            if ($request->getPost('settings') == "Update") {
                $userVal->password = md5($request->getPost('password'));
                $userVal->save();


                //Now we need to send an email
                //$this->sendSuccessEmail($user)

                $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                return $this->redirect()->toUrl("/admin/company");
            }

            if ($request->getPost('invoice') == "Update") {
                $post = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );


                if ($post['invoice_name']['name'] == "") {
                    $error_messages[] = "You must upload an image file for invoice with one<br>
                                           of the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                } else {
                    $invoice_filename = stripslashes($post['invoice_name']['name']);
                    $invoice_extension = $this->getExtension($invoice_filename);
                    $invoice_extension = strtolower($invoice_extension);

                    if (($invoice_extension != "jpg") && ($invoice_extension != "jpeg") && ($invoice_extension != "png") && ($invoice_extension != "gif") && ($invoice_extension != "bmp")) {
                        $error_messages[] = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }

                if ($is_validation_error == 0) {
                    $invoice_image = time() . rand(100, 110) . '.' . $invoice_extension;
                    $invoice_newname = "public/upload/client/" . $invoice_image;
                    $copied = copy($post['invoice_name']['name'], $invoice_newname);

                    $img = Image::make($post['invoice_name']['tmp_name']);
                    $img->save($invoice_newname);

                    $invoice = new \Admin\Model\Invoice();
                    $invoice->comapny_id = $id;
                    $invoice->file_name = $invoice_image;

                    $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
                    $invoice->created_by = $auth_users->id;

                    $invoice->invoice_date = $request->getPost('invoice_date');
                    $invoice->save();

                    $this->flashMessenger()->setNamespace('success')->addMessage('User has been updated.');
                    return $this->redirect()->toUrl("/admin/company");
                }
            }
        } else {

            $results = $user->find($id);
        }

        return new ViewModel(array(
            'error_messages' => $error_messages,
            'post_data' => $post_data,
            'results' => $results,
            'employer_profile' => $employer_profile,
            'is_top_employer' => $is_top_employer,
            'company_id' => $id,
            'invoice' => $invoice
        ));
    }

    public function addjobAction() {
        //echo "<pre>";
        //print_r($_POST);
        //exit;

        $is_validation_error = 0;
        $post_data = array();
        $error_messages = array();
        $preferred_role = array();
        $selected_categories_id = array();
        $selected_industry_id = array();
        $selected_preferred_role_id = array();
        $selected_business_type_id = array();

        $request = $this->getRequest();
        $job = new \Admin\Model\Job();

        $company_id = $this->params()->fromRoute('cid');

        //For Login user
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->orderBy('name', 'ASC')
                ->get();

        $country = $EloquentDb::table('chk_country')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();

        //Job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->where ('status','=',1)
                ->orderBy('name', 'ASC')
                ->get();

        //Business type
        $business_type = $EloquentDb::table('chk_business_type')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        //Department
        $department = $EloquentDb::table('chk_department')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        foreach ($department as $val) {
            //Department
            $role = $EloquentDb::table('chk_preferred_role')
                    ->select('id', 'name')
                    ->where('department_id', $val["id"])
                    ->where('status', 1)
                    ->orderBy('name', 'ASC')
                    ->get();

            $preferred_role[$val["id"]] = $role;
        }


        if ($request->isPost()) {

            $post_data = $request->getPost();

            //For spot light
            if ($request->getPost('spot_light_used') == 1) {
                $post = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );

                if ($post['spotlight_job_logo']['name'] == "") {
                    $error_messages[] = "You must upload an image file for spotlight job with one<br>
                                           of the following extensions:jpg, jpeg, gif, png, bmp";
                    $is_validation_error = 1;
                } else {
                    $filename = stripslashes($post['spotlight_job_logo']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);

                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[] = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }

                if ($request->getPost('spotlight_job_content') == "") {
                    $error_messages[] = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }

                if ($request->getPost('spotlight_job_order') == "") {
                    $error_messages[] = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }

                if ($request->getPost('spotlight_job_gorup') == "") {
                    $error_messages[] = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }

            if ($is_validation_error == 0) {

                $unique_job_id = $this->getUniqueJobId($company_id);

                $job->employer_id = $company_id;
                $job->created_by = $auth_users->id;
                $job->created_at = date("Y-m-d H:i:s");
                $job->job_id = $unique_job_id;

                $job->job_title = $request->getPost('job_title');
                $job->no_of_vacencies = $request->getPost('no_of_vacencies');
                $job->job_location = $request->getPost('job_location');

                if ($request->getPost('job_location') == "Other") {
                    $job->other_location = $request->getPost('other_location');
                }

                $job->age_limit_from = $request->getPost('age_limit_from');
                $job->job_level = implode(",", $request->getPost('job_level'));
                $job->job_type = implode(",", $request->getPost('job_type'));
                $job->posting_date = $request->getPost('posting_date');
                $job->job_deadline = $request->getPost('job_deadline');
                $job->job_education = $request->getPost('job_education');
                $job->salary_range = $request->getPost('salary_range');
                $job->other_benefits = $request->getPost('other_benefits');
                $job->job_experienced = $request->getPost('job_experienced');
                $job->job_requirement = $request->getPost('job_requirement');
                $job->job_description = $request->getPost('job_description');
                $job->spot_light_used = $request->getPost('spot_light_used');
                $job->show_company_status = $request->getPost('show_company_status');

                $job->online_cv_received = $request->getPost('online_cv_received');

                if ($_POST["email_cv"] == "EmailCV") {
                    $job->email_cv_received = $request->getPost('email_cv_received');
                } else {
                    $job->email_cv_received = "";
                }

                if ($_POST["hardcopy"] == "hardcopy") {
                    $job->hardcopy_cv_received = $request->getPost('hardcopy_cv_received');
                } else {
                    $job->hardcopy_cv_received = "";
                }

                //Age limit
                if ($_POST["type_age"] == "type_age") {
                    $job->age_limit_other = $request->getPost('age_limit_other');
                    $job->age_limit_to = "";
                    $job->age_limit_from = "";
                } else {
                    $job->age_limit_other = "";
                    $job->age_limit_to = $request->getPost('age_limit_to');
                    $job->age_limit_from = $request->getPost('age_limit_from');
                }

                if ($_POST["save"] == "Save") {
                    $job->status = $request->getPost('status');
                }

                if ($_POST["preview"] == "Preview") {
                    $job->status = 0;
                }

                //Spotlight
                if ($request->getPost('spot_light_used') == 1) {

                    $image_name = time() . '.' . $extension;
                    $newname = "public/upload/job/" . $image_name;
                    $copied = copy($post['spotlight_job_logo']['name'], $newname);

                    $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                    // resize image
                    //$img->fit(300, 200);
                    $img->resize(150, 150);

                    // save image
                    $img->save($newname);

                    $job->spotlight_job_content = $request->getPost('spotlight_job_content');
                    $job->spotlight_job_logo = $image_name;
                    $job->spotlight_job_order = $request->getPost('spotlight_job_order');
                    $job->spotlight_job_gorup = $request->getPost('spotlight_job_gorup');
                }


                $job->save();
                $job_id = $job->id;

                //echo $job_id." --------------";
                //exit;

                if ($request->getPost('job_location') == "Within Bangladesh") {
                    foreach ($request->getPost('division_id') as $division_id) {
                        $EloquentDb::table('chk_job_division')->insert(
                                [
                                    'job_id' => $job_id,
                                    'division_id' => $division_id
                                ]
                        );
                    }
                }

                if ($request->getPost('job_location') == "Outside Bangladesh") {
                    foreach ($request->getPost('country_id') as $country_id) {
                        $EloquentDb::table('chk_job_country')->insert(
                                [
                                    'job_id' => $job_id,
                                    'country_id' => $country_id
                                ]
                        );
                    }
                }

                foreach ($request->getPost('job_category_id') as $category_id) {
                    $EloquentDb::table('chk_job_category_map')->insert(
                            [
                                'job_id' => $job_id,
                                'category_id' => $category_id
                            ]
                    );
                }

                foreach ($request->getPost('job_industry_id') as $industry_id) {
                    $EloquentDb::table('chk_job_industry_map')->insert(
                            [
                                'job_id' => $job_id,
                                'industry_id' => $industry_id
                            ]
                    );
                }


                foreach ($request->getPost('preferred_role_id') as $preferred_role_id) {
                    $EloquentDb::table('chk_job_role')->insert(
                            [
                                'job_id' => $job_id,
                                'preferred_role_id' => $preferred_role_id
                            ]
                    );
                }

                foreach ($request->getPost('business_type_id') as $business_type_id) {
                    $EloquentDb::table('chk_job_business')->insert(
                            [
                                'job_id' => $job_id,
                                'business_type_id' => $business_type_id
                            ]
                    );
                }

                //add job circular
                $EloquentDb::table('chk_job_circular_type')->insert(
                        [
                            'job_id' => $job_id,
                            'job_circular_id' => 5
                        ]
                );

                if ($request->getPost('status') == "1") {
                    //update chk_job_category
                    $update = new \Admin\Model\General();
                    $update->inrementJob($EloquentDb, $request->getPost('job_category_id'), 'chk_job_category');

                    //update chk_job_industry
                    $update = new \Admin\Model\General();
                    $update->inrementJob($EloquentDb, $request->getPost('job_industry_id'), 'chk_job_industry');
                }

                if ($_POST["save"] == "Save") {
                    $this->flashMessenger()->setNamespace('success')->addMessage('Employer job has been added.');
                    return $this->redirect()->toUrl("/admin/company/jobs/" . $company_id);
                }

                if ($_POST["preview"] == "Preview") {
                    return $this->redirect()->toUrl("/job/show/" . $job_id);
                }
            }
        }


        //Add back button
        //$prev_url = $this->getRequest()->getHeader('Referer')->getUri();        

        return new ViewModel(array(
            'division' => $division,
            'country' => $country,
            'job_category' => $job_category,
            'job_industry' => $job_industry,
            'business_type' => $business_type,
            'department' => $department,
            'preferred_role' => $preferred_role,
            'post_data' => $post_data,
            'company_id' => $company_id,
            'selected_categories_id' => $selected_categories_id,
            'selected_industry_id' => $selected_industry_id,
            'selected_preferred_role_id' => $selected_preferred_role_id,
            'selected_business_type_id' => $selected_business_type_id,
            'error_messages' => $error_messages
        ));
    }

    public function editjobAction() {
        $is_validation_error = 0;
        $error_messages = array();
        $post_data = array();
        $selected_categories_id = array();
        $selected_industry_id = array();
        $selected_preferred_role_id = array();
        $selected_business_type_id = array();
        $selected_division_id = array();
        $selected_country_id = array();

        $request = $this->getRequest();
        //$id = $this->params()->fromRoute('id');
        $job = new \Admin\Model\Job();

        $company_id = $this->params()->fromRoute('cid');
        $job_id = $this->params()->fromRoute('jid');

        //For Login user
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->get();

        $country = $EloquentDb::table('chk_country')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Job category
        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->orderBy('name', 'ASC')
                ->get();

        //Job category
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->orderBy('name', 'ASC')
                ->get();

        //Business type
        $business_type = $EloquentDb::table('chk_business_type')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Department
        $department = $EloquentDb::table('chk_department')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        foreach ($department as $val) {
            //Department
            $role = $EloquentDb::table('chk_preferred_role')
                    ->select('id', 'name')
                    ->where('department_id', $val["id"])
                    ->where('status', 1)
                    ->orderBy('name', 'ASC')
                    ->get();

            $preferred_role[$val["id"]] = $role;
        }

        //Selected devision
        $job_division = $EloquentDb::table('chk_job_division')
                ->select('division_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($job_division as $val) {
            $selected_division_id[] = $val["division_id"];
        }

        //Selected country
        $job_country = $EloquentDb::table('chk_job_country')
                ->select('country_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($job_country as $val) {
            $selected_country_id[] = $val["country_id"];
        }

        //Selected preferred role
        $job_role = $EloquentDb::table('chk_job_role')
                ->select('preferred_role_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($job_role as $val) {
            $selected_preferred_role_id[] = $val["preferred_role_id"];
        }

        //Selected preferred business
        $job_business = $EloquentDb::table('chk_job_business')
                ->select('business_type_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($job_business as $val) {
            $selected_business_type_id[] = $val["business_type_id"];
        }


        //Selected job category
        $job_category_map = $EloquentDb::table('chk_job_category_map')
                ->select('category_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($job_category_map as $val) {
            $selected_categories_id[] = $val["category_id"];
        }

        //Selected job industry
        $chk_job_industry_map = $EloquentDb::table('chk_job_industry_map')
                ->select('industry_id')
                ->where('job_id', $job_id)
                ->get();

        foreach ($chk_job_industry_map as $val) {
            $selected_industry_id[] = $val["industry_id"];
        }


        if ($request->isPost()) {
            // get post data 
            $post_data = $request->getPost();

            $jobValue = $job->find($job_id);

            if ($request->getPost('spot_light_used') == 1) {
                $post = array_merge_recursive(
                        $request->getPost()->toArray(), $request->getFiles()->toArray()
                );

                if ($post['spotlight_job_logo']['name'] != "") {
                    $filename = stripslashes($post['spotlight_job_logo']['name']);
                    $extension = $this->getExtension($filename);
                    $extension = strtolower($extension);

                    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") && ($extension != "bmp")) {
                        $error_messages[] = "Unknown image extension. <br>
                                                You can upload the following extensions:jpg, jpeg, gif, png, bmp";
                        $is_validation_error = 1;
                    }
                }

                if ($request->getPost('spotlight_job_content') == "") {
                    $error_messages[] = "Spotlight job content field required.";
                    $is_validation_error = 1;
                }

                if ($request->getPost('spotlight_job_order') == "") {
                    $error_messages[] = "Spotlight job order field required.";
                    $is_validation_error = 1;
                }

                if ($request->getPost('spotlight_job_gorup') == "") {
                    $error_messages[] = "Spotlight job gorup field required.";
                    $is_validation_error = 1;
                }
            }

            if ($is_validation_error == 0) {

                $jobValue->modified_by = $auth_users->id;
                $jobValue->updated_at = date("Y-m-d H:i:s");

                $jobValue->job_title = $request->getPost('job_title');
                $jobValue->no_of_vacencies = $request->getPost('no_of_vacencies');
                $jobValue->job_location = $request->getPost('job_location');

                if ($request->getPost('job_location') == "Other") {
                    $jobValue->other_location = $request->getPost('other_location');
                }

                $jobValue->age_limit_from = $request->getPost('age_limit_from');
                $jobValue->job_level = implode(",", $request->getPost('job_level'));
                $jobValue->job_type = implode(",", $request->getPost('job_type'));
                $jobValue->posting_date = $request->getPost('posting_date');
                $jobValue->job_deadline = $request->getPost('job_deadline');
                $jobValue->job_education = $request->getPost('job_education');
                $jobValue->salary_range = $request->getPost('salary_range');
                $jobValue->other_benefits = $request->getPost('other_benefits');
                $jobValue->job_experienced = $request->getPost('job_experienced');
                $jobValue->job_requirement = $request->getPost('job_requirement');
                $jobValue->job_description = $request->getPost('job_description');
                $jobValue->spot_light_used = $request->getPost('spot_light_used');
                $jobValue->show_company_status = $request->getPost('show_company_status');
                $jobValue->status = $request->getPost('status');

                $jobValue->online_cv_received = $request->getPost('online_cv_received');

                if ($_POST["email_cv"] == "EmailCV") {
                    $jobValue->email_cv_received = $request->getPost('email_cv_received');
                } else {
                    $jobValue->email_cv_received = "";
                }

                if ($_POST["hardcopy"] == "hardcopy") {
                    $jobValue->hardcopy_cv_received = $request->getPost('hardcopy_cv_received');
                } else {
                    $jobValue->hardcopy_cv_received = "";
                }

                //Age limit
                if ($_POST["type_age"] == "type_age") {
                    $jobValue->age_limit_other = $request->getPost('age_limit_other');
                    $jobValue->age_limit_to = "";
                    $jobValue->age_limit_from = "";
                } else {
                    $jobValue->age_limit_other = "";
                    $jobValue->age_limit_to = $request->getPost('age_limit_to');
                    $jobValue->age_limit_from = $request->getPost('age_limit_from');
                }

                //spotlight
                if ($request->getPost('spot_light_used') == 1) {
                    if ($post['spotlight_job_logo']['name'] != "") {
                        $image_name = time() . '.' . $extension;
                        $newname = "public/upload/job/" . $image_name;
                        $copied = copy($post['spotlight_job_logo']['name'], $newname);

                        $img = Image::make($post['spotlight_job_logo']['tmp_name']);

                        // resize image
                        //$img->fit(300, 200);
                        $img->resize(150, 150);

                        // save image
                        $img->save($newname);

                        //Delete previous images                                                
                        $spotlight_job_logo = $EloquentDb::table('chk_jobs')
                                ->select('spotlight_job_logo')
                                ->where('id', $job_id)
                                ->first();

                        unlink("public/upload/job/" . $spotlight_job_logo["spotlight_job_logo"]);

                        $jobValue->spotlight_job_logo = $image_name;
                    }

                    $jobValue->spotlight_job_content = $request->getPost('spotlight_job_content');
                    $jobValue->spotlight_job_order = $request->getPost('spotlight_job_order');
                    $jobValue->spotlight_job_gorup = $request->getPost('spotlight_job_gorup');
                } else {
                    $jobValue->spotlight_job_content = "";
                    $jobValue->spotlight_job_logo = "";
                    $jobValue->spotlight_job_order = "";
                    $jobValue->spotlight_job_gorup = "";
                }


                $jobValue->save();

                //Update division
                $EloquentDb::table('chk_job_division')->where('job_id', $job_id)->delete();

                if ($request->getPost('job_location') == "Within Bangladesh") {
                    foreach ($request->getPost('division_id') as $division_id) {

                        $EloquentDb::table('chk_job_division')->insert(
                                [
                                    'job_id' => $job_id,
                                    'division_id' => $division_id
                                ]
                        );
                    }
                }

                //Update country
                $EloquentDb::table('chk_job_country')->where('job_id', $job_id)->delete();

                if ($request->getPost('job_location') == "Outside Bangladesh") {
                    foreach ($request->getPost('country_id') as $country_id) {

                        $EloquentDb::table('chk_job_country')->insert(
                                [
                                    'job_id' => $job_id,
                                    'country_id' => $country_id
                                ]
                        );
                    }
                }


                //Update job_category_map
                $EloquentDb::table('chk_job_category_map')->where('job_id', $job_id)->delete();

                foreach ($request->getPost('job_category_id') as $category_id) {
                    $EloquentDb::table('chk_job_category_map')->insert(
                            [
                                'job_id' => $job_id,
                                'category_id' => $category_id
                            ]
                    );
                }

                //Update chk_job_industry_map
                $EloquentDb::table('chk_job_industry_map')->where('job_id', $job_id)->delete();

                foreach ($request->getPost('job_industry_id') as $industry_id) {
                    $EloquentDb::table('chk_job_industry_map')->insert(
                            [
                                'job_id' => $job_id,
                                'industry_id' => $industry_id
                            ]
                    );
                }

                //Update chk_job_role
                $EloquentDb::table('chk_job_role')->where('job_id', $job_id)->delete();

                foreach ($request->getPost('preferred_role_id') as $preferred_role_id) {
                    $EloquentDb::table('chk_job_role')->insert(
                            [
                                'job_id' => $job_id,
                                'preferred_role_id' => $preferred_role_id
                            ]
                    );
                }

                //Update chk_job_business
                $EloquentDb::table('chk_job_business')->where('job_id', $job_id)->delete();

                foreach ($request->getPost('business_type_id') as $business_type_id) {
                    $EloquentDb::table('chk_job_business')->insert(
                            [
                                'job_id' => $job_id,
                                'business_type_id' => $business_type_id
                            ]
                    );
                }

                //
                if ($request->getPost('status') == "0") {
                    //update chk_job_category
                    $update = new \Admin\Model\General();
                    $update->decrementJob($EloquentDb, $request->getPost('job_category_id'), 'chk_job_category');

                    //update chk_job_industry
                    $update = new \Admin\Model\General();
                    $update->decrementJob($EloquentDb, $request->getPost('job_industry_id'), 'chk_job_industry');
                }

                if ($_POST["save"] == "Save") {
                    $this->flashMessenger()->setNamespace('success')->addMessage('Update successfull.');
                    return $this->redirect()->toUrl("/admin/company/jobs/" . $company_id);
                }

                if ($_POST["preview"] == "Preview") {
                    return $this->redirect()->toUrl("/job/show/" . $job_id);
                }
            }
        } else {

            $results = $job->find($job_id);
        }


        $view = new ViewModel(array(
            'error_messages' => $error_messages,
            'results' => $results,
            'post_data' => $post_data,
            'division' => $division,
            'country' => $country,
            'job_category' => $job_category,
            'job_industry' => $job_industry,
            'business_type' => $business_type,
            'department' => $department,
            'preferred_role' => $preferred_role,
            'selected_division_id' => $selected_division_id,
            'selected_country_id' => $selected_country_id,
            'selected_preferred_role_id' => $selected_preferred_role_id,
            'selected_business_type_id' => $selected_business_type_id,
            'selected_categories_id' => $selected_categories_id,
            'selected_industry_id' => $selected_industry_id,
            'company_id' => $company_id
        ));

        $view->setTemplate('admin/company/addjob.phtml');
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

    function sendSuccessEmail($user) {
        // send email 
        $template = "email/seeker";
        $htmlViewPart = new ViewModel();
        $htmlViewPart->setTerminal(true)
                ->setTemplate($template)
                ->setVariables(array(
                    'full_name' => $user->full_name
        ));

        $content = $this->getServiceLocator()->get('viewrenderer')->render($htmlViewPart);


        // make a header as html  
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));
        // instance mail   
        $mail = new Mail\Message();
        $mail->setBody($body); // will generate our code html from template.phtml  
        $mail->setFrom('admin@chakri.com', 'Chakri.com');
        $mail->setTo($user->email, $user->full_name);
        $mail->setSubject('This is the first step to finding a perfect job for you. Welcome to Chakri! ');

        try {
            $transport = new Mail\Transport\Sendmail();
            $transport->send($mail);
        } catch (Exception $e) {
            var_dump($e->getMessages());
        }
    }

    public function getUniqueJobId($company_id) {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $chk_users = $EloquentDb::table('chk_users')
                ->select('full_name')
                ->where('id', $company_id)
                ->first();

        $company_name = str_replace(" ", "_", strtolower(trim($chk_users["full_name"])));

        /*
          $chk_jobs =  $EloquentDb::table('chk_jobs')
          ->select('id')
          ->where('employer_id', $company_id)
          ->orderBy('id','DESC')
          ->first();
         */
        $chk_jobs = $EloquentDb::table('chk_jobs')
                ->where('employer_id', $company_id)
                ->count();

        return $company_name . "_" . ($chk_jobs + 1);
    }

    public function deleteinvoiceAction() {

        $id = $this->params()->fromRoute('cid');

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $chk_invoice = $EloquentDb::table('chk_invoice')
                ->select('*')
                ->where('id', $id)
                ->first();

        //delete invoice
        unlink("public/upload/client/" . $chk_invoice["file_name"]);

        $EloquentDb::table('chk_invoice')->where('id', $id)->delete();


        $this->flashMessenger()->setNamespace('success')->addMessage('This Invoice has been deleted.');
        return $this->redirect()->toUrl("/admin/company/employeredit/" . $chk_invoice["comapny_id"]);
    }

}
