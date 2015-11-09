<?php

namespace Job\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Admin\Model\Job;
use Carbon\Carbon;
use Zend\View\Model\JsonModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class IndexController extends AbstractActionController {

    public function indexAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $itemsPerPage = 10;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //$all_request_data = array_merge($allGetValues, $post_data);
        // division
        $division = $EloquentDb::table('chk_division')
                ->select('id', 'name')
                //->where('status', 1)
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

        //$allGetValues = $this->params()->fromQuery();
        //$employee_id = (!empty($allGetValues['emp'])) ? $allGetValues['emp'] : 0;

        $allGetValues = $this->params()->fromQuery();

        //$catid = $allGetValues['catId'];
        // $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;


        if (isset($allGetValues['page'])) {
            unset($allGetValues['page']);
        }

        if (isset($allGetValues['search_btn'])) {
            unset($allGetValues['search_btn']);
        }

        $all_request_data = $allGetValues;

        /* if (count($post_data) > 0) {
          $allGetValues = array();
          unset($post_data['search_btn']);
          $all_request_data = $post_data;

          // $allGetValues  = $post_data;
          } else {
          $all_request_data = $allGetValues;
          } */

        /* foreach ($all_request_data as $key => $value) {
          if (!empty($value)) {
          $allGetValues[$key] = $value;
          }
          } */

        //$page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;

        $search_by_users = array();

        $query = $EloquentDb::table('chk_jobs')
                ->leftjoin('chk_users', 'chk_jobs.employer_id', '=', 'chk_users.id')
                ->leftjoin('chk_employer_profile', 'chk_jobs.employer_id', '=', 'chk_employer_profile.user_id')
                ->select('chk_jobs.*', 'chk_users.full_name', 'chk_employer_profile.company_logo');



        foreach ($all_request_data as $key => $search_data) {
            switch ($key) {
                case "catId":
                    if (!empty($search_data)) {

                        $s_cat = $EloquentDb::table('chk_job_category')
                                ->select('id', 'name')
                                ->where('id', $search_data)
                                ->first();
                        
                        $search_by_users['category'] = $s_cat['name'];

                        //$query->where('job_category_id', '=', $search_data);
                        $query->join('chk_job_category_map', 'chk_jobs.id', '=', 'chk_job_category_map.job_id')
                                   ->where('chk_job_category_map.category_id', '=', $search_data);
                    }
                    break;
                case "indId":
                    if (!empty($search_data)) {

                        $s_cat = $EloquentDb::table('chk_job_industry')
                                ->select('id', 'name')
                                ->where('id', $search_data)
                                ->first();
                        $search_by_users['industry'] = $s_cat['name'];
                        //$query->where('job_industry_id', '=', $search_data );
                        $query->join('chk_job_industry_map', 'chk_jobs.id', '=', 'chk_job_industry_map.job_id')
                                ->where('chk_job_industry_map.industry_id', '=', $search_data);
                    }
                    break;
                case "emp":
                    if (!empty($search_data)) {

                        $s_cat = $EloquentDb::table('chk_users')
                                ->select('id', 'full_name')
                                ->where('id', $search_data)
                                ->first();
                        $search_by_users['company'] = $s_cat['full_name'];

                        $query->where('employer_id', '=', $search_data);
                    }
                    break;
                case "job_type":
                    if (!empty($search_data)) {

                        $search_by_users['Job Type'] = $search_data;
                        $query->where('chk_jobs.job_type', 'like', '%' . $search_data . '%');
                    }
                    break;


                case "job_title":
                    if (!empty($search_data)) {

                        $search_by_users['Job Title'] = $search_data;
                        
                        //$search_data = strtolower($search_data);
                        
                        $query->whereRaw( 'LOWER(`job_title`) like "%'. $search_data .'%"'  );
                        $query->orWhereRaw('LOWER(`full_name`) like "%' . $search_data . '%" and show_company_status = "No" ');
                        
                        
                        //$query->where('chk_jobs.job_title', 'like', '%' . $search_data . '%');
                        //$query->orWhere('chk_users.full_name', 'like', '%' . $search_data . '%');
                    }
                    break;
                //job_type
                //job_level
                case "job_level":
                    if (!empty($search_data)) {
                        $search_by_users['job_level'] = $search_data;
                        $query->where('chk_jobs.job_level', 'like', '%' . $search_data . '%');
                    }
                    break;
                case "loc":
                    if (!empty($search_data)) {


                        if ($tbl_division_join != 1) {
                            $tbl_division_join = 1;
                            $query->join('chk_job_division', 'chk_jobs.id', '=', 'chk_job_division.job_id');
                        }
                        $s_cat = $EloquentDb::table('chk_division')
                                ->select('id', 'name')
                                ->where('id', $search_data)
                                ->first();
                        $search_by_users['location'] = $s_cat['name'];

                        $query->where('chk_job_division.division_id', '=', $search_data);
                    }
                    break;

                case "company":
                    if (!empty($search_data)) {

                        $search_by_users['company'] = $search_data;
                        //$query->join('chk_job_division', 'chk_jobs.id', '=', 'chk_job_division.job_id');
                        $query->where('chk_users.full_name', 'like', '%' . $search_data . '%');
                    }
                    break;
                case "degree_id":

                    if (!empty($search_data)) {

                        //$search_by_users['company']=$search_data;
                        //$query->join('chk_job_division', 'chk_jobs.id', '=', 'chk_job_division.job_id');
                        $s_cat = $EloquentDb::table('chk_education_degree')
                                ->select('id', 'name')
                                ->where('id', $search_data)
                                ->first();

                        $search_by_users['education'] = $s_cat['name'];

                        if ($tbl_degree_join != 1) {
                            $tbl_degree_join = 1;
                            $query->join('chk_education_degree', 'chk_jobs.job_education', '=', 'chk_education_degree.id');
                        }
                        $query->where('chk_jobs.job_education', '=', $search_data);
                    }

                    break;

                case "experienced":
                    if (!empty($search_data)) {

                        $search_by_users['experienced'] = $search_data;
                        //$query->join('chk_job_division', 'chk_jobs.id', '=', 'chk_job_division.job_id');
                        $query->where('chk_jobs.job_experienced', '=', $search_data);
                    }
                    break;
                
                case "circular_type":
                    if (!empty($search_data)) {

                        if ($search_data == 'newspaper') {
                            $search_by_users['Newspaper job'] = 'Newspaper';
                            $query->where('chk_jobs.job_circular_type', '=', 2);
                        }

                        if ($search_data == 'govtjob') {
                            if ($tbl_circular_job != 1) {
                                $tbl_circular_job = 1;
                                $query->join('chk_job_circular_type', 'chk_jobs.id', '=', 'chk_job_circular_type.job_id');
                            }

                            $search_by_users['Newspaper job'] = 'Govt. Job';
                            $query->where('chk_job_circular_type.job_circular_id', '=', 2);
                        }
                        
                         if ($search_data == 'walkinjob') {
                            if ($tbl_circular_job != 1) {
                                $tbl_circular_job = 1;
                                $query->join('chk_job_circular_type', 'chk_jobs.id', '=', 'chk_job_circular_type.job_id');
                            }

                            $search_by_users['Newspaper job'] = 'Walk in';
                            $query->where('chk_job_circular_type.job_circular_id', '=', 3);
                        }
                    }
                    break;

                case "jtype":
                    if (!empty($search_data)) {

                        if ($search_data == 'newjob') {
                            $search_by_users['Job type'] = 'Newly Posted Job';
                            $query->where('chk_jobs.posting_date', '=', date('Y-m-d'));
                        }

                        if ($search_data == 'deadline_tomorrow') {
                            $search_by_users['Job type'] = 'Deadline Tomorrow';
                            $query->where('chk_jobs.job_deadline', '=', date("Y-m-d", time() + 86400));
                        }


                        //$query->where('chk_jobs.job_circular_type', '=', $search_data);
                    }
                    break;
                    
                default; 
                    //$query->where('chk_jobs.job_deadline', '>=', date('Y-m-d'));
                    
            }
        }

        //$query->where('employer_id', '=', $employee_id);
        //$query->where('chk_jobs.job_deadline', '>=', date('Y-m-d'));
        $query->where('chk_jobs.status', '=', 1);
        $query->orderBy('chk_jobs.job_circular_type', 'ASC');
        $query->orderBy('chk_jobs.id', 'DESC');


        /*
         * ->join('chk_job_division', 'chk_division.id', '=', 'chk_job_division.division_id')
          ->where('chk_job_division.job_id', '=', $id)

         * 
         * 
         */


        /* $result = $query->get();
          //print_r($result);
          $queries =  $EloquentDb::getQueryLog();
          $last_query = end($queries);
          print_r($last_query);
          exit ();
         * 
         */



        //$result = $query->get();
        // $padapter = new \Application\Paginator\Adapter($query);
        $padapter = new \Application\Paginator\Jobadapter($query);

        $total_count = $padapter->count();

        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);

        //$memcache_name = "company_jobs_" . $employee_id . "_" . $page;


        /* if (!$this->getServiceLocator()
          ->get('memcached')->hasItem($memcache_name)) {

          $this->getServiceLocator()
          ->
         * 
         * get('memcached')
          ->setItem($memcache_name, $paginator); */

        $results = array();
        $i = 0;
        foreach ($paginator as $jobs) {

            if ($jobs['job_location'] == "Within Bangladesh") {
                $country = $EloquentDb::table('chk_division')
                        ->select('chk_division.name')
                        ->join('chk_job_division', 'chk_division.id', '=', 'chk_job_division.division_id')
                        ->where('chk_job_division.job_id', '=', $jobs['id'])
                        ->get();

                $new_division = array();
                foreach ($country as $val) {
                    $new_division[] = $val["name"];
                }

                $comma_division = implode(", ", $new_division);
                $job_location = $comma_division;
            } elseif ($jobs['job_location'] == "Outside Bangladesh") {
                $country = $EloquentDb::table('chk_country')
                        ->select('chk_country.name')
                        ->join('chk_job_country', 'chk_country.id', '=', 'chk_job_country.country_id')
                        ->where('chk_job_country.job_id', '=', $jobs['id'])
                        ->get();

                $new_country = array();
                foreach ($country as $val) {
                    $new_country[] = $val["name"];
                }

                $comma_country = implode(", ", $new_country);
                $job_location = $comma_country;
            } elseif ($jobs['job_location'] == "Other") {
                $job_location = $jobs['other_location'];
            }


            $prefered_buisness = $EloquentDb::table('chk_job_business')
                    ->join('chk_business_type', 'chk_business_type.id', '=', 'chk_job_business.business_type_id')
                    ->select('chk_business_type.name')
                    ->where('job_id', $jobs['id'])
                    ->get();


            $company_job_count = $EloquentDb::table('chk_jobs')
                    ->where('employer_id', $jobs['employer_id'])
                    ->where('chk_jobs.status', '=', 1)
                    ->where('chk_jobs.job_deadline', '>=', date('Y-m-d'))
                    ->count();



            $results[$i] = $jobs;
            $results[$i]['prefer_job_location'] = $job_location;
            $results[$i]['prefered_buisness'] = $prefered_buisness;
            $results[$i]['total_job_openings'] = $company_job_count;
            $i++;
        }
        //}

        $results_per_page = count($results);

        $offset_start = ($page - 1) * $itemsPerPage;

        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'total_count' => $total_count,
            'results' => $results,
            'result_per_page' => $results_per_page,
            'offset_start' => $offset_start,
            'industries' => $industries,
            'buisness_type' => $business_type,
            'functional_type' => $functional_type,
            'degrees' => $degrees,
            'companies' => $companies,
            'total_count' => $total_count,
            'search_by_users' => $search_by_users
        ));
    }

    public function showAction() {
        $location = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

         $id = $this->params()->fromRoute('id');

        //
        $job_view = new \Admin\Model\General();
        $job_view->jobView($EloquentDb, $id);

        $job = job::select('chk_jobs.*', 'chk_jobs.trashed as job_trashed', 'chk_users.*')
                ->leftjoin('chk_users', 'chk_jobs.employer_id', '=', 'chk_users.id')
                ->where('chk_jobs.id', '=', $id)
                ->first();



        if ($job->job_trashed == 1) {
            $this->redirect()->toUrl('/job/notavailable');
        }

        
        //Job location
        if ($job->job_location == "Within Bangladesh") {
            $country = $EloquentDb::table('chk_division')
                    ->select('chk_division.name')
                    ->join('chk_job_division', 'chk_division.id', '=', 'chk_job_division.division_id')
                    ->where('chk_job_division.job_id', '=', $id)
                    ->get();

            $new_division = array();
            foreach ($country as $val) {
                $new_division[] = $val["name"];
            }

            $comma_division = implode(", ", $new_division);
            $job_location = $comma_division;
        } elseif ($job->job_location == "Outside Bangladesh") {
            $country = $EloquentDb::table('chk_country')
                    ->select('chk_country.name')
                    ->join('chk_job_country', 'chk_country.id', '=', 'chk_job_country.country_id')
                    ->where('chk_job_country.job_id', '=', $id)
                    ->get();


            $new_country = array();
            foreach ($country as $val) {
                $new_country[] = $val["name"];
            }

            $comma_country = implode(", ", $new_country);
            $job_location = $comma_country;
        } elseif ($job->job_location == "Other") {
            $job_location = $job->other_location;
        }


        if (!empty($job->posting_date)) {
            $created = new Carbon($job->posting_date);
            $now = Carbon::now();

            $difference = ($created->diff($now)->days < 1) ? 'today' : $created->diffForHumans($now);

            $job->posting_date_diff = $difference;
        }



        //For employer profile
        $company = $EloquentDb::table('chk_employer_profile')
                ->select('*')
                ->where('user_id', $job->employer_id)
                ->first();

        //Selected job industry
        $job_industry = $EloquentDb::table('chk_job_industry')
                ->select('chk_job_industry.name')
                ->join('chk_job_industry_map', 'chk_job_industry.id', '=', 'chk_job_industry_map.industry_id')
                ->where('chk_job_industry_map.job_id', $id)
                ->get();


        //Selected job Functional area/ department
        $job_department = $EloquentDb::table('chk_department')
                ->select('chk_department.name')
                ->join('chk_preferred_role', 'chk_department.id', '=', 'chk_preferred_role.department_id')
                ->join('chk_job_role', 'chk_preferred_role.id', '=', 'chk_job_role.preferred_role_id')
                ->where('chk_job_role.job_id', $id)
                ->get();

        //Selected job role
        $job_role = $EloquentDb::table('chk_preferred_role')
                ->select('chk_preferred_role.name')
                ->join('chk_job_role', 'chk_preferred_role.id', '=', 'chk_job_role.preferred_role_id')
                ->where('chk_job_role.job_id', $id)
                ->get();

        //Selected job business
        $job_business = $EloquentDb::table('chk_business_type')
                ->select('chk_business_type.name')
                ->join('chk_job_business', 'chk_business_type.id', '=', 'chk_job_business.business_type_id')
                ->where('chk_job_business.job_id', $id)
                ->get();


        //job category
        $job_category = $EloquentDb::table('chk_job_category_map')
                ->select('category_id')
                ->where('job_id', $id)
                ->get();


        //Similar jobs
        $similar_jobs = $EloquentDb::table('chk_jobs')
                ->select('chk_jobs.*', 'chk_users.full_name')
                ->join('chk_job_category_map', 'chk_jobs.id', '=', 'chk_job_category_map.job_id')
                ->join('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                ->whereIn('chk_job_category_map.category_id', $job_category)
                ->where('chk_jobs.job_deadline', '>=', date("Y-m-d"))
                ->where('chk_jobs.status', '=', 1)
                ->where('chk_jobs.id', '!=', $id)
                ->orderBy('chk_jobs.id', 'DESC')
                ->take(6)
                ->get();

        
        
        foreach ($similar_jobs as $val) {
            
            $jid = $val["id"];
            if ($val["job_location"] == "Within Bangladesh") {
                $country = $EloquentDb::table('chk_division')
                        ->select('chk_division.name')
                        ->join('chk_job_division', 'chk_division.id', '=', 'chk_job_division.division_id')
                        ->where('chk_job_division.job_id', '=', $jid)
                        ->get();

                $new_division = array();
                foreach ($country as $val) {
                    $new_division[] = $val["name"];
                }

                $comma_division = implode(", ", $new_division);

                $location[$id] = $comma_division;
            } elseif ($val["job_location"] == "Outside Bangladesh") {
                $country = $EloquentDb::table('chk_country')
                        ->select('chk_country.name')
                        ->join('chk_job_country', 'chk_country.id', '=', 'chk_job_country.country_id')
                        ->where('chk_job_country.job_id', '=', $jid)
                        ->get();

                $new_country = array();
                foreach ($country as $val) {
                    $new_country[] = $val["name"];
                }

                $comma_country = implode(", ", $new_country);
                $location[$val["id"]] = $comma_country;
            } elseif ($val["job_location"] == "Other") {
                $location[$id] = $val["other_location"];
            }
        }

        
        //Company total job
        $company_job = new \Admin\Model\General();
        $company_job_count = $company_job->companyTotalJobOpening($EloquentDb, $job->employer_id);

        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        if ($auth_users->id != "") {
            $chk_cv = $EloquentDb::table('chk_cv')
                    ->select('id', 'resume_title')
                    ->where('user_id', $auth_users->id)
                    ->get();
        }

        $job_status = $EloquentDb::table('chk_job_application')
                ->select('app_status')
                ->where('job_id', $id)
                ->where('user_id', $auth_users->id)
                ->get();
        
         /*$queries =  $EloquentDb::getQueryLog();
          $last_query = end($queries);
          print_r($last_query);
          exit ();*/
        
         
        return new ViewModel(array(
            'id' => $id,
            'job' => $job,
            'company_job_count' => $company_job_count,
            'company' => $company,
            'job_industry' => $job_industry,
            'job_department' => $job_department,
            'job_role' => $job_role,
            'job_business' => $job_business,
            'similar_jobs' => $similar_jobs,
            'location' => $location,
            'job_location' => $job_location,
            'chk_cv' => $chk_cv,
            'job_status' => $job_status
        ));
    }

    public function savejobAction() {

        $already_save = false;

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post_data = $request->getPost();
            $job_id = $post_data['id'];
            $type = $post_data['type'];
            $expected_salary = $post_data['expected_salary'];
            $cv_id = $post_data['cv_id'];

            $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

            $result = $EloquentDb::table('chk_job_application')
                    ->select('*')
                    ->where('job_id', $job_id)
                    ->where('user_id', $auth_users->id)
                    ->get();

            if (count($result) == 0) {
                $EloquentDb::table('chk_job_application')->insert(
                        [
                            'job_id' => $job_id,
                            'cv_id' => $cv_id,
                            'expected_salary' => $expected_salary,
                            'user_id' => $auth_users->id,
                            'app_status' => $type
                        ]
                );
                $message = "you successfully $type this job ";
            } else {
                if ($result[0]["app_status"] == "save") {
                    if ($type == 'apply') {
                        $already_save = true;
                        // update 
                        $EloquentDb::table('chk_job_application')
                                ->where('job_id', $job_id)
                                ->where('user_id', $auth_users->id)
                                ->update(array(
                                    'app_status' => 'apply',
                                    'expected_salary' => $expected_salary,
                                    'cv_id' => $cv_id
                        ));
                        $message = 'you successfully applied for this job';
                    } else {

                        $message = "you already save this job. ";
                    }
                } elseif ($result[0]["app_status"] == "apply") {
                    // $already_save = true;   
                    $message = 'You already applied for this job. ';
                }
            }
        }

        $htmlViewPart = new ViewModel();
        $htmlViewPart->setTerminal(true);

        $jsonModel = new JsonModel();
        $jsonModel->setVariables(array(
            'jsonVar1' => 'jsonVal2',
            'alreadysave' => $already_save,
            'message' => $message,
            'jsonArray' => array(1, 2, 3, 4, 5, 6),
            'success' => true
        ));

        return $jsonModel;
    }

    function notavailableAction() {

        return new ViewModel();
    }

}
