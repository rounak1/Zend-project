<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $tjobs = new \Application\Model\Index();
        //$results = $tjobs::where('status', '=', '1')->orderBy('name', 'asc')->get();

        $tindustry = new \Application\Model\Industry();
        $industryresults = $tindustry::where('status', '=', '1')->orderBy('name', 'asc')->get();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        /* $jobCategory = $EloquentDb::table('chk_job_category')
          ->select(
          'chk_job_category.id',
          'chk_job_category.name',
          $EloquentDb::raw("(SELECT COUNT(chk_jobs.id)

          ) AS totaljob"
          )
          )
          ->leftJoin('chk_job_category_map','chk_job_category_map.category_id','=','chk_job_category.id')
          ->leftJoin('chk_jobs','chk_jobs.id','=','chk_job_category_map.job_id')
          ->where('chk_job_category.status', '=', 1)
          ->where('chk_jobs.status', '=', 1)
          ->orderBy('chk_job_category.name', 'ASC')
          ->groupBy('chk_job_category.id')
          ->get();
         * 
         */

        $jobCategory = $EloquentDb::select(
                        $EloquentDb::raw("SELECT chk_job_category.id,
                                        chk_job_category.name,
                                        count(jobs.id) as totalJob
                                        
                                        FROM chk_job_category

                                        LEFT JOIN chk_job_category_map 
                                        ON chk_job_category_map.category_id = chk_job_category.id

                                        LEFT JOIN (SELECT chk_jobs.id from chk_jobs where chk_jobs.status=1 ) jobs
                                             ON jobs.id = chk_job_category_map.job_id
                                        
                                       
                                        WHERE chk_job_category.status = 1  
                                        
                                        
                                        GROUP BY chk_job_category.id 
                                        Order By chk_job_category.name asc
                                "));



        $jobIndustry = $EloquentDb::select(
                        $EloquentDb::raw("SELECT chk_job_industry.id,
                                            chk_job_industry.name,
                                            count(jobs.id) as totalJob
                                            
                                            FROM chk_job_industry
                                            
                                            LEFT JOIN chk_job_industry_map 
                                            ON chk_job_industry_map.industry_id = chk_job_industry.id
                                            
                                            LEFT JOIN (SELECT chk_jobs.id from chk_jobs where chk_jobs.status=1) jobs
                                            ON jobs.id = chk_job_industry_map.job_id
                                            
                                            WHERE chk_job_industry.status = 1
                                           
                                            GROUP BY chk_job_industry.id
                                            Order By chk_job_industry.name asc
                                        "
        ));


        // Spotlight job
        $spotJob = $EloquentDb::table('chk_jobs')
                ->select(
                        'chk_jobs.id', 'chk_jobs.job_title', 'chk_jobs.job_company_name', 'chk_jobs.job_title', 'chk_jobs.spotlight_job_logo', 'chk_jobs.spotlight_job_gorup', 'chk_jobs.spotlight_job_order', 'chk_jobs.job_id', 'chk_users.full_name', 'chk_users.id AS employerId', 'chk_employer_profile.company_logo'
                )
                ->leftJoin('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                ->leftJoin('chk_employer_profile', 'chk_employer_profile.user_id', '=', 'chk_users.id')
                ->where('chk_jobs.spot_light_used', '=', 1)
                ->where('chk_jobs.job_deadline', '>=', date('Y-m-d'))
                ->where('chk_jobs.posting_date', '<=', date("Y-m-d"))
                ->where('chk_jobs.status', '=', 1)
                ->orderBy('chk_jobs.posting_date', 'DESC')
                //->orderBy('chk_jobs.spotlight_job_gorup', 'ASC')
                //->orderBy('chk_jobs.spotlight_job_order', 'ASC')
                //->take(6)
                ->get();

        // Spotlight job
        $topEmployer = $EloquentDb::table('chk_users')
                ->select(
                        'chk_users.id', 'chk_users.full_name', 'chk_employer_profile.company_logo'
                )
                ->join('chk_employer_profile', 'chk_employer_profile.user_id', '=', 'chk_users.id')
                ->where('chk_employer_profile.is_top_employer', '=', 1)
                ->where('chk_users.user_status', '=', 'active')
                ->where('chk_users.trashed', '=', 0)
                ->orderBy('chk_employer_profile.updated_at', "DESC")
                ->get();

        //top University
        $topUniversity = $EloquentDb::table('chk_institute')
                ->select(
                        'chk_institute.id', 'chk_institute.name', 'chk_institute.institute_logo'
                )
                ->where('chk_institute.is_top', '=', 'Yes')
                ->where('chk_institute.status', '=', 1)
                ->orderBy('chk_institute.updated_at', "DESC")
                ->get();

        //print_r($topUniversity);exit;

        $events = $EloquentDb::table('chk_event')
                ->select('*')
                ->where('status', 1)
                ->where('event_date', '>=', date("Y-m-d"))
                ->orderBy('event_date', "ASC")
                ->get();

        //location 
        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->get();



        $spotlightJob = array();

        $group = 0;
        $order = 0;
        $employerId = 0;

        foreach ($spotJob as $val) {

            if ($val["spotlight_job_gorup"] == 0 || $val["spotlight_job_gorup"] == "") {
                $spotlight_job_gorup = $group;
            } else {
                $spotlight_job_gorup = $val["spotlight_job_gorup"];
            }

            if ($val["employerId"] == "") {
                $employerId = $employerId;
            } else {
                $employerId = $val["employerId"];
            }

            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["id"] = $val["id"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["job_id"] = $val["job_id"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["employerId"] = $val["employerId"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["job_title"] = $val["job_title"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["job_company_name"] = $val["job_company_name"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["spotlight_job_logo"] = $val["spotlight_job_logo"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["full_name"] = $val["full_name"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["company_logo"] = $val["company_logo"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["spotlight_job_gorup"] = $val["spotlight_job_gorup"];
            $spotlightJob[$employerId][$spotlight_job_gorup][$val["id"]]["spotlight_job_order"] = $val["spotlight_job_order"];


            $group++;
            $order++;
            $employerId++;
        }


        $this->layout('layout/home');
        return new ViewModel(array(
            //'totalJobs' => $results,
            'total_industry_results' => $industryresults,
            'spotlightJob' => $spotlightJob,
            'topEmployer' => $topEmployer,
            'events' => $events,
            'jobCategory' => $jobCategory,
            'jobIndustry' => $jobIndustry,
            'division' => $division,
            'topUniversity' => $topUniversity
        ));
    }

    public function aboutAction() {
        return new ViewModel();
    }

    public function contactAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form = new \Application\Form\ContactUsForm();
            $form->setInputFilter($form->inputFilter);
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $contact_us = new \Application\Model\ContactUs();
                $contact_us->name = $request->getPost('name');
                $contact_us->email = $request->getPost('email');
                $contact_us->phone = $request->getPost('phone');
                $contact_us->message = $request->getPost('message');
                $contact_us->issue = $request->getPost('issue');
                $contact_us->save();

                $this->flashMessenger()->setNamespace('success')->addMessage('Your query has been sent successfully.');
            } else {
                foreach ($form->inputFilter->getMessages() as $messageId => $error)
                    foreach ($error as $er)
                        $this->flashMessenger()->setNamespace('error')->addMessage(strtok(ucfirst($messageId), "_-") . " : " . $er);
                $post_data = $request->getPost()->toArray();
            }
            return $this->redirect()->toUrl("/contact");
        }
    }

    public function feedbackAction() {
        $success_messages = array();
        $request = $this->getRequest();
        $post_data = $request->getPost();
        $feedback = new \Application\Model\Feedback();
        if ($request->isPost()) {
            $feedback->type = $request->getPost('type');
            $feedback->email = $request->getPost('email');
            $feedback->subject = $request->getPost('subject');
            $feedback->message = $request->getPost('message');
            $feedback->captcha = $request->getPost('captcha');
            $feedback->save();
            $this->flashMessenger()->setNamespace('success')->addMessage('Your Feedback has been sent successfully.');
            return $this->redirect()->toUrl("/");
        }
        $prev_url = $this->getRequest()->getHeader('Referer')->getUri();
        return new ViewModel(array(
            'error_messages' => $error_messages,
            'success_messages' => $success_messages,
            'feedback_ok' => TRUE
        ));
    }

    public function jobDetailsAction() {
        return new ViewModel();
    }

    public function categoryDetailsAction() {

        $itemsPerPage = 10;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        //$catid = $allGetValues['catId'];
        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;

        // $offset = ($page - 1) * $itemsPerPage + 1;

        if (isset($allGetValues['page'])) {
            unset($allGetValues['page']);
        }

        if (count($post_data) > 0) {
            $allGetValues = array();
            unset($post_data['search_btn']);
            $all_request_data = $post_data;

            // $allGetValues  = $post_data;
        } else {
            $all_request_data = $allGetValues;
        }

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }


        //$all_request_data = array_merge($allGetValues, $post_data);
        // division
        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->get();

        //Business type
        $business_type = $EloquentDb::table('chk_business_type')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Functional category
        $functional_type = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Institute
        $industries = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        // Academic degree 
        $degrees = $EloquentDb::table('chk_education_degree')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        //Company list
        $companies = $EloquentDb::table('chk_users')
                ->select('id', 'full_name')
                ->where('user_type', 4)
                ->where('user_status', '=', 'active')
                ->orderBy('full_name', 'ASC')
                ->get();

        /* $query = $EloquentDb::table('chk_jobs')
          ->select('chk_jobs.*');
         */

        $query = $EloquentDb::table('chk_jobs')
                ->join('chk_users', 'chk_jobs.employer_id', '=', 'chk_users.id')
                ->join('chk_employer_profile', 'chk_jobs.employer_id', '=', 'chk_employer_profile.user_id')
                ->select('chk_jobs.*', 'chk_users.full_name', 'chk_employer_profile.company_logo');

        $query->where('employer_id', '=', $employee_id);
        $query->where('chk_jobs.status', '=', 1);

        foreach ($all_request_data as $key => $search_data) {
            switch ($key) {
                case "catId":
                    if (!empty($search_data)) {

                        $query->where('job_category_id', 'like', '%' . $search_data . '%');
                    }
                    break;
                case "employeeId":
                    if (!empty($search_data)) {

                        $query->where('employer_id', '=', $search_data);
                    }
                    break;
            }
        }

        //$query->orderBy('job_category_id', 'asc');
        $query->orderBy('chk_jobs.id', 'asc');

        $padapter = new \Application\Paginator\Adapter($query);
        $total_count = $padapter->count();
        $paginator = new Paginator($padapter);
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);




        return new ViewModel(array(
            'catId' => $id,
            'catDetails' => $catDetails,
            'paginator' => $paginator,
            'params' => $allGetValues,
            'industries' => $industries,
            'buisness_type' => $business_type,
            'functional_type' => $functional_type,
            'degrees' => $degrees,
            'companies' => $companies,
            'total_count' => $total_count
        ));
    }

    public function jobsAction() {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        //$all_request_data = array_merge($allGetValues, $post_data);
        // division
        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->get();

        //Business type
        $business_type = $EloquentDb::table('chk_business_type')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Functional category
        $functional_type = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->where('status', 1)
                ->get();

        //Institute
        $industries = $EloquentDb::table('chk_job_industry')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        // Academic degree 
        $degrees = $EloquentDb::table('chk_education_degree')
                ->select('id', 'name')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        //Company list
        $companies = $EloquentDb::table('chk_users')
                ->select('id', 'full_name')
                ->where('user_type', 4)
                ->where('user_status', '=', 'active')
                ->orderBy('full_name', 'ASC')
                ->get();

        $query = $EloquentDb::table('chk_jobs')
                ->select('chk_jobs.*')
                ->orderBy('job_category_id', 'asc')
                ->get();

        $padapter = new ArrayAdapter($query);
        $paginator = new Paginator($padapter);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1))
                ->setItemCountPerPage(10);


        return new ViewModel(array(
            'paginator' => $paginator,
            'industries' => $industries,
            'buisness_type' => $business_type,
            'functional_type' => $functional_type,
            'degrees' => $degrees,
            'companies' => $companies
        ));
    }

    public function jobByRolesAction() {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

//        $roles = $EloquentDb::table('chk_preferred_role')
//                ->join('chk_department','chk_department.id','=','chk_preferred_role.department_id')
//                ->select('chk_department.name as deptName','chk_department.id as deptId','chk_preferred_role.name as roleName','chk_preferred_role.id')                
//                ->where('chk_preferred_role.status', 1)
//                ->groupBy('deptName')
//                ->get();

        $department = $EloquentDb::table('chk_department')
                ->select('name', 'id')
                ->where('status', 1)
                ->orderBy('name', 'ASC')
                ->get();

        foreach ($department as $val) {
            $role = $EloquentDb::table('chk_preferred_role')
                    ->select('id', 'name')
                    ->where('department_id', $val["id"])
                    ->where('status', 1)
                    ->orderBy('name', 'ASC')
                    ->get();

            $departments[$val["name"]] = $role;
        }

        return new ViewModel(array('departments' => $departments));
    }

    public function jobByCompaniesAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $companies = $EloquentDb::table('chk_jobs')
                ->join('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                ->select('chk_jobs.employer_id', $EloquentDb::raw('count(employer_id) as total_job, status'), 'chk_users.full_name')
                ->where('employer_id', '!=', '')
                // not admin job 2= admin type
                ->where('employer_id', '!=', 2)
                ->where('chk_jobs.status', '=', 1)
                ->groupBy('employer_id')
                ->get();

        return new ViewModel(array('companies' => $companies));
    }

    public function advancedJobSearchAction() {
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                ->orderBy('name', 'ASC')
                ->get();

        $job_category = $EloquentDb::table('chk_job_category')
                ->select('id', 'name')
                ->where('status', '=', 1)
                ->orderBy('name', 'ASC')
                ->get();


        return new ViewModel(array('divisions' => $division, 'categories' => $job_category));
    }

    public function topEmployerAction() {
        $id = $this->params()->fromRoute('id');

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Delete previous job images
        $employer = $EloquentDb::table('chk_employer_profile')
                ->join('chk_users', 'chk_employer_profile.user_id', '=', 'chk_users.id')
                ->select('chk_users.id', 'chk_users.full_name', 'company_url', 'company_description', 'company_service', 'company_banner', 'company_logo', 'address')
                ->where('chk_employer_profile.user_id', $id)
                ->where('chk_employer_profile.is_top_employer', '=', 1)
                ->where('chk_users.user_status', '=', 'active')
                ->where('chk_users.trashed', '=', 0)
                ->first();

        return new ViewModel(array(
            'employer' => $employer
        ));
    }

    public function topuniversityAction() {
        $id = $this->params()->fromRoute('id');

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        // query
        $topUniversity = $EloquentDb::table('chk_institute')
                ->select(
                        'chk_institute.id', 'chk_institute.name', 'chk_institute.institute_logo', 'chk_institute.about_courses', 'chk_institute.organigation_status'
                )
                ->where('chk_institute.is_top', '=', 'Yes')
                ->where('chk_institute.status', '=', 1)
                ->where('chk_institute.id', '=', $id)
                ->first();

        return new ViewModel(array(
            'topunv' => $topUniversity
        ));
    }

    public function cvViewAction() {
        return new ViewModel();
    }

    public function pageAction() {
        $alias = $this->params()->fromRoute('alias');

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Delete previous job images
        $page = $EloquentDb::table('chk_pages')
                ->select('*')
                ->where('alias', $alias)
                ->first();

        return new ViewModel(array(
            'page' => $page
        ));
    }

    public function spotlightAction() {
        $alias = $this->params()->fromRoute('alias');

        $this->layout('layout/spotlight');
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Delete previous job images
        $job = $EloquentDb::table('chk_jobs')
                ->select('*')
                ->where('job_id', $alias)
                ->first();


        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        if ($auth_users->id != "") {
            $chk_cv = $EloquentDb::table('chk_cv')
                    ->select('id', 'resume_title')
                    ->where('user_id', $auth_users->id)
                    ->get();
        }

        $job_status = $EloquentDb::table('chk_job_application')
                ->select('app_status')
                ->where('job_id', $job["id"])
                ->where('user_id', $auth_users->id)
                ->get();

        /*
          echo $job["id"]."--";
          echo "<pre>";
          print_r($job_status);
          exit;
         */


        return new ViewModel(array(
            'job' => $job,
            'job_status' => $job_status,
            'chk_cv' => $chk_cv,
        ));
    }

    public function abuseAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $EloquentDb = $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

            $user_id = ($auth_users->id) ? $auth_users->id : NULL;
            $request = $this->getRequest();
            $url = substr($request->getHeader('referer'), 9);
            $email = $request->getPost('email');

            $abuse_status = $EloquentDb::table('chk_report_abuse')
                    ->where('url', substr($request->getHeader('referer'), 9))
                    ->orWhere(function($query) {
                        $query->where('user_id', '=', $user_id)
                        ->where('email', '=', $email);
                    })
                    ->get();

            if ($abuse_status) {
                $this->flashMessenger()->setNamespace('error')->addMessage('Sorry, you have already given feedback to this');
                return $this->redirect()->toUrl($url);
            } else {
                $report = $job = new \Admin\Model\ReportAbuse();
                $report->user_id = $user_id;
                $report->name = $auth_users->full_name;
                $report->user_type = $auth_users->user_type;
                $report->email = $request->getPost('email');
                $report->url = $url;
                $report->page = $request->getPost('page');
                $report->abuse_type = $request->getPost('abuse_type');
                $report->message = $request->getPost('message');
                $report->status = 1;
                $report->created_at = date("Y-m-d H:i:s");
                $report->updated_at = date("Y-m-d H:i:s");
                $report->save();
            }
        }

        $this->flashMessenger()->setNamespace('success')->addMessage('Thanks for your feedback');
        return $this->redirect()->toUrl($url);
    }
    
    public function serviceAction() {
        return new ViewModel(array(
            
        ));
    }

}
