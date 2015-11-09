<?php

namespace Chkapi\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\AbstractRestfulController;
//use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use yajra\Datatables\Datatables as Datatables;
use Carbon\Carbon;
use \Admin\Model\Job;
use \Chkuser\Model\User;

class ApiController extends AbstractRestfulController {

    public function jobAction() {


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $jobs = Job::
                leftjoin('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id'
                )
                ->orderBy('chk_jobs.id', 'DESC')
                //->where('chk_jobs.job_circular_type', '=', '')
                ->where('chk_jobs.status', '=', '1')
                ->where('chk_jobs.job_deadline', '>=', date("Y-m-d"))
                ->where('chk_jobs.posting_date', '<=', date("Y-m-d"))
                ->select(array(
            'chk_jobs.id',
            'chk_jobs.employer_id',
            'chk_jobs.job_title',
            'chk_users.full_name',
            'chk_jobs.posting_date',
            'chk_jobs.job_deadline',
            'chk_jobs.status'
        ));





        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    return '<a href="/admin/company/editjob/' . $job->employer_id . '/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>'
                            . '<a href="/job/show/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Details</a>';
                })
                ->editColumn('posting_date', function ($job) {
                    if ($job->posting_date != '') {
                        $dt = Carbon::createFromFormat('Y-m-d', $job->posting_date);
                        return $dt->toFormattedDateString();
                    } else {
                        return $job->posting_date;
                        // return null;
                    }
                })
                ->editColumn('job_deadline', function ($job) {
                    // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                    $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);
                    //return $job->job_deadline->format('Y/m/d');
                    return $dt->toFormattedDateString();
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                ->removeColumn('id')
                ->make(true);



        return new JsonModel($output);


        // $this->getResponse()->getHeaders()->addHeaders(array('Content-Type'=>'application/json;charset=UTF-8'));
        //return $this->getResponse()->setContent($output);
        // return $this->getResponse()->setContent($output);
        //echo $output;
        //return false;
    }

    public function newsjobAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        // 1 is newspapaer id 
        $circular_array = array(1);

        /*
          $jobs = Job::
          join('chk_employer_profile',
          'chk_employer_profile.user_id',
          '=',
          'chk_jobs.employer_id'
          )
          ->orderBy('chk_jobs.id', 'DESC')
          ->where('chk_jobs.job_circular_type', 'LIKE', '%1%')

          ->select(array(
          'chk_jobs.id',
          'chk_jobs.job_title',
          'chk_employer_profile.company_name',
          'chk_jobs.job_type',
          'chk_jobs.job_deadline',
          'chk_jobs.status'
          ));
         * 
         */

        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id')
                ->where('type', '=', 'Newspaperjob')
                ->first();

        $jobs = Job::
                join('chk_job_circular_type', 'chk_job_circular_type.job_id', '=', 'chk_jobs.id'
                )
                ->orderBy('chk_jobs.id', 'DESC')
                ->where('chk_job_circular_type.job_circular_id', '=', $job_circular['id'])
                ->select(array(
            'chk_jobs.id',
            'chk_jobs.job_title',
            'chk_jobs.job_company_name',
            'chk_jobs.job_type',
            'chk_jobs.job_deadline',
            'chk_jobs.posting_date',
            'chk_jobs.status'
        ));

        /*
          $jobs = Job::
          orderBy('id', 'DESC')
          ->where('job_circular_type', 'LIKE', '%1%')

          ->select(array(
          'id',
          'job_title',
          'job_company_name',
          'posting_date',
          'job_deadline',
          'status'
          ));
         */

        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    return '<a href="/admin/job/newsjobedit/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>'
                            . '<a href="/job/show/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Details</a>';
                })
                ->editColumn('job_deadline', function ($job) {
                    if ($job->job_deadline != "" && $job->job_deadline != "0000-00-00") {
                        // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                        $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);
                        //return $job->job_deadline->format('Y/m/d');
                        return $dt->toFormattedDateString();
                    }
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                ->editColumn('posting_date', function ($job) {
                    if ($job->posting_date && $job->posting_date != "0000-00-00") {
                        $dt = Carbon::createFromFormat('Y-m-d', $job->posting_date);
                        return $dt->toFormattedDateString();
                    }
                })
                ->removeColumn('id')
                ->make(true);



        return new JsonModel($output);


        // $this->getResponse()->getHeaders()->addHeaders(array('Content-Type'=>'application/json;charset=UTF-8'));
        //return $this->getResponse()->setContent($output);
        // return $this->getResponse()->setContent($output);
        //echo $output;
        //return false;
    }

    public function govtJobAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id')
                ->where('type', '=', 'Govjob')
                ->first();

        $jobs = Job::
                join('chk_job_circular_type', 'chk_job_circular_type.job_id', '=', 'chk_jobs.id'
                )
                ->orderBy('chk_jobs.id', 'DESC')
                ->where('chk_job_circular_type.job_circular_id', '=', $job_circular['id'])
                ->select(array(
            'chk_jobs.id',
            'chk_jobs.job_title',
            'chk_jobs.job_company_name',
            'chk_jobs.job_type',
            'chk_jobs.job_deadline',
            'chk_jobs.status'
        ));




        /* $jobs = Job::
          orderBy('id', 'DESC')
          ->where('job_circular_type', 'LIKE', '%2%')

          ->select(array(
          'id',
          'job_title',
          'job_company_name',
          'job_type',
          'job_deadline',
          'status'
          )); */



        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    return '<a href="/admin/job/govtjobedit/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> '
                            . '<a href="/job/show/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Details</a>';
                })
                ->editColumn('job_deadline', function ($job) {
                    // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                    $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);
                    //return $job->job_deadline->format('Y/m/d');
                    return $dt->toFormattedDateString();
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                ->removeColumn('id')
                ->make(true);



        return new JsonModel($output);


        // $this->getResponse()->getHeaders()->addHeaders(array('Content-Type'=>'application/json;charset=UTF-8'));
        //return $this->getResponse()->setContent($output);
        // return $this->getResponse()->setContent($output);
        //echo $output;
        //return false;
    }

    public function spotlightjobAction() {


        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        // 1 is newspapaer id 
        $circular_array = array(1);


        $jobs = Job::
                leftJoin('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id'
                )
                ->orderBy('chk_jobs.updated_at', 'DESC')
                ->where('chk_jobs.spot_light_used', 1)
                ->select(array(
            'chk_jobs.id',
            'chk_jobs.job_title',
            'chk_users.full_name',
            'chk_jobs.job_type',
            'chk_jobs.job_deadline',
            'chk_jobs.status'
        ));


        /*
          $jobs = \Admin\Model\Spotlightjob::

          join('chk_jobs',
          'chk_jobs.spot_light_job_id',
          '=',
          'chk_spotlight_job.id')
          -> leftjoin('chk_users',
          'chk_users.id',
          '=',
          'chk_jobs.employer_id'
          )
          ->orderBy('chk_spotlight_job.id', 'DESC')
          //->where('job_circular_type', 'LIKE', '%3%')
          //->where('spot_light_job_id','=','1')
          ->where('chk_jobs.status','=','1')
          ->select(array(
          'chk_spotlight_job.id',
          'chk_jobs.job_title',
          'chk_users.full_name',
          'chk_jobs.job_type',
          'chk_jobs.job_deadline',
          'chk_jobs.status'
          ));
         */



        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    //return '<a href="/admin/job/spotlightedit/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                })
                ->editColumn('job_deadline', function ($job) {
                    // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                    if ($job->job_deadline != '') {
                        $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);

//return $job->job_deadline->format('Y/m/d');
                        return $dt->toFormattedDateString();
                    } else {
                        return $job->job_deadline;
                    }
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                //  ->removeColumn('id')
                ->make(true);



        return new JsonModel($output);


        // $this->getResponse()->getHeaders()->addHeaders(array('Content-Type'=>'application/json;charset=UTF-8'));
        //return $this->getResponse()->setContent($output);
        // return $this->getResponse()->setContent($output);
        //echo $output;
        //return false;
    }

    public function usersAction() {

        $users = User::
                select(array(
                    'id',
                    'full_name',
                    'email',
                    'user_role',
                    'user_status'
                ))
                ->orderBy('id', 'DESC')
        ;


        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {
                    return '<a href="/admin/seekeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function topEmployerAction() {

        /*
          $users = User::
          select(array(
          'id',
          'full_name',
          'email',
          'user_role',
          'user_status'
          ))
          ->orderBy('id', 'DESC')
          ;
         * 
         */

        $users = User::
                join('chk_employer_profile', 'chk_employer_profile.user_id', '=', 'chk_users.id'
                )
                ->orderBy('chk_users.id', 'DESC')
                ->where('chk_employer_profile.is_top_employer', '=', '1')
                ->where('chk_users.trashed', '=', '0')
                ->select(array(
            'chk_users.id',
            'chk_users.full_name',
            'chk_users.email',
            'chk_users.user_role',
            'chk_users.user_status'
        ));


        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {
                    return '<a href="/admin/company/employeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function seekerAction() {
        $users = User::
                select(array(
                    'id',
                    'full_name',
                    'user_name',
                    'email',
                    'created_at',
                    'user_status'
                ))
//                ->orderBy('id', 'DESC')
                ->where('user_type', '=', '3')
                ->where('trashed', '=', '0')
                ->orderBy('created_at', 'DESC');


        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {
                    return '<a href="/admin/seekeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>'
                    ;
                })
                ->editColumn('created_at', function ($users) {
                    if ($users->created_at != '') {
                        $dt = Carbon::createFromFormat('Y-m-d H:i:s', $users->created_at);
                        return $dt->toFormattedDateString();
                    } else {
                        return $job->posting_date;
                        // return null;
                    }
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function employerAction() {

        $users = User::
                select(array(
                    'id',
                    'full_name',
                    'email',
                    'mobile_no',
                    'user_status',
                    'user_role'
                ))
                ->orderBy('id', 'DESC')
                ->where('user_type', '=', '4')
                ->where('trashed', '=', '0')
        ;


        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {
                    return '
                            <a class="btn btn-primary btn-xs" href="/admin/company/jobs/' . $users->id . '"><i class="fa fa-link"></i>Jobs</a>                                                                                                
                            <a class="btn btn-primary btn-xs" href="/admin/company/serviceslist/' . $users->id . '"><i class="fa fa-th-list"></i> Package</a>
                           '
                            . '<a href="/admin/company/employeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>'

                    ;
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function jobarchiveAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $jobs = Job::
                select(array(
                    'chk_jobs.id',
                    'chk_jobs.job_title',
                    'chk_users.full_name',
                    'chk_jobs.posting_date',
                    'chk_jobs.job_deadline',
                    'chk_jobs.job_circular_type',
                    'chk_jobs.status',
                    'chk_jobs.employer_id'
                ))
                ->join('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id'
                )
                ->orderBy('chk_jobs.id', 'DESC')
                ->where('chk_jobs.job_deadline', '<', date("Y-m-d"))
        ;

        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    //$html .= '<a href="#" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';

                    /*
                      if($job->job_circular_type == 1)
                      {
                      $html .= '<a href="/admin/job/repost/'.$job->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Repost</a>';
                      }
                     * 
                     */

                    $html .= '<a href="/admin/job/repost/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Repost</a>';

                    return $html;
                })
                ->editColumn('posting_date', function ($job) {
                    if ($job->posting_date != '') {
                        $dt = Carbon::createFromFormat('Y-m-d', $job->posting_date);
                        return $dt->toFormattedDateString();
                    } else {
                        return $job->posting_date;
                        // return null;
                    }
                })
                ->editColumn('job_deadline', function ($job) {
                    // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                    $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);
                    //return $job->job_deadline->format('Y/m/d');
                    return $dt->toFormattedDateString();
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                ->removeColumn('id')
                ->make(true);



        return new JsonModel($output);


        // $this->getResponse()->getHeaders()->addHeaders(array('Content-Type'=>'application/json;charset=UTF-8'));
        //return $this->getResponse()->setContent($output);
        // return $this->getResponse()->setContent($output);
        //echo $output;
        //return false;
    }

    public function walkininterviewAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id')
                ->where('type', '=', 'Walk in interview')
                ->first();

        $jobs = Job::
                join('chk_job_circular_type', 'chk_job_circular_type.job_id', '=', 'chk_jobs.id'
                )
                ->orderBy('chk_jobs.id', 'DESC')
                ->where('chk_job_circular_type.job_circular_id', '=', $job_circular['id'])
                ->select(array(
            'chk_jobs.id',
            'chk_jobs.job_company_name',
            'chk_jobs.job_type',
            'chk_jobs.job_level',
            'chk_jobs.job_deadline',
            'chk_jobs.published',
            'chk_jobs.posting_date',
            'chk_jobs.status'
        ));


        $output = Datatables::of($jobs)
                ->addColumn('action', function ($job) {
                    return '<a href="/admin/job/editwalkin/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                            <a href="/job/show/' . $job->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i>Details</a>'
//                            .'
//                           <form style="display:inline-block;" method="POST" action="/admin/job/deletewalkin/' . $job->id . '" accept-charset="UTF-8">                                      
//                                <button style="border-bottom-left-radius:0;border-top-left-radius:0;" class="btn btn-xs btn-danger" type="button" data-toggle="modal" data-target="#confirmDelete" data-title="Walk in interview" data-message="Are you sure you want to delete this?">
//                                    <i class="glyphicon glyphicon-trash"></i> Delete
//                                </button>
//                            </form>
//                             '                          
                    ;
                })
                ->editColumn('job_deadline', function ($job) {
                    // $instance = Carbon::createFromFormat('y-m-d', $job->job_deadline);
                    $dt = Carbon::createFromFormat('Y-m-d', $job->job_deadline);
                    //return $job->job_deadline->format('Y/m/d');
                    return $dt->toFormattedDateString();
                })
                ->editColumn('posting_date', function ($job) {
                    $dt = Carbon::createFromFormat('Y-m-d', $job->posting_date);
                    return $dt->toFormattedDateString();
                })
                ->editColumn('status', function ($job) {
                    if ($job->status == 1) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                })
                ->removeColumn('id')
                ->make(true);

        return new JsonModel($output);
    }

    public function cvAction() {
        $request = $this->getRequest();
        $post_data = $request->getPost();
        $job_id = $post_data['job_id'];


        $users = User::
                select(array(
                    'chk_job_application.job_id',
                    'chk_job_application.id as job_application_id',
                    'chk_job_application.is_short',
                    'chk_job_application.app_rating',
                    'chk_job_application.job_id',
                    'chk_job_application.expected_salary',
                    'chk_users.id',
                    'chk_users.full_name',
                    'chk_users.user_name',
                    'chk_seeker_profile.age',
                    'chk_cv.experience'
                ))
                ->join('chk_job_application', 'chk_job_application.user_id', '=', 'chk_users.id')
                ->leftJoin('chk_cv', 'chk_cv.id', '=', 'chk_job_application.cv_id')
                ->leftJoin('chk_seeker_profile', 'chk_users.id', '=', 'chk_seeker_profile.user_id')
                ->where('chk_job_application.job_id', '=', $job_id)
                ->where('chk_job_application.app_status', '=', 'apply')

        ;

        //echo "<pre>";
        //print_r($users);
        //exit;

        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {
                    $html = '<div class="btn-group">
                                <a data-application_id="' . $users->job_application_id . '" data-user="' . $users->id . '"  data-user_name="' . $users->full_name . '"  data-job="' . $users->job_id . '" id="' . "folder_" . $users->job_id . '" class="applay_folder btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-folder"></i> Folder</a>                                
                                <a target="_blank" data-application_id="' . $users->job_application_id . '" data-user-id="' . $users->id . '" data-user_name="' . $users->full_name . '" class="resume_view btn btn-primary btn-xs" href="/resume/' . $users->id . '"><i class="fa fa-sort-amount-desc"></i> View</a>
                                ';

                    if ($users->is_short == 0) {
                        $html .= '<a data-application_id="' . $users->job_application_id . '" data-user-id="' . $users->id . '" data-user_name="' . $users->full_name . '" class="applay_shortlist btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-sort-amount-desc"></i> Shortlist</a>';
                    }

                    //if($users->app_rating <= 0)
                    {
                        $html .= '<a data-application_id="' . $users->job_application_id . '" data-user_name="' . $users->full_name . '" id="' . "rating_" . $users->job_id . '"   class="apply_rating btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-thumbs-o-up"></i> Rating</a>';
                    }

                    $html .= '<a data-user="' . $users->id . '" data-job_id="' . $users->job_id . '" data-user_name="' . $users->full_name . '" class="apply_message btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-folder"></i> Message</a>                             
                                </div>';

                    if ($users->is_short == 1) {
                        $html .= '<span class="label label-success pull-right"> Short Listed</span>';
                    }

                    if ($users->app_rating > 0) {
                        $html .= '<span class="label label-info pull-right"> Rating (' . $users->app_rating . ') </span>';
                    }

                    return $html;


                    //return '<a href="/admin/seekeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function foldercvAction() {
        $request = $this->getRequest();
        $post_data = $request->getPost();
        $folder_id = $post_data['folder_id'];
        
        $users = User::
                select(array(
                    //'chk_job_application.job_id',
                    //'chk_job_application.id as job_application_id',
                    //'chk_job_application.is_short',
                    //'chk_job_application.app_rating',
                    //'chk_job_application.job_id',
                    //'chk_job_application.expected_salary',                    
                    'chk_users.id',
                    'chk_users.full_name',
                    'chk_users.email',
                    'chk_users.mobile_no',
                    'chk_employer_preferred_folder.job_application_id'
                        //'chk_seeker_profile.age',
                        //'chk_cv.job_experience'                   
                ))

                //->join('chk_job_application', 'chk_job_application.user_id', '=', 'chk_users.id')
                ->join('chk_employer_preferred_folder', 'chk_users.id', '=', 'chk_employer_preferred_folder.user_id')
                //->leftJoin('chk_cv', 'chk_cv.id', '=', 'chk_job_application.cv_id')
                //->leftJoin('chk_seeker_profile', 'chk_users.id', '=', 'chk_seeker_profile.user_id')
                ->where('chk_employer_preferred_folder.folder_id', '=', $folder_id)
                ->orderBy('chk_employer_preferred_folder.id', 'DESC');
        //->where('chk_job_application.app_status', '=', 'apply');
        //echo "<pre>";
        //print_r($users);
        //exit;

        $output = Datatables::of($users)
                ->addColumn('action', function ($users) {

                    /*
                      $html = '<div class="btn-group">
                      <a data-application_id="'.$users->job_application_id.'" data-user="'.$users->id.'"  data-user_name="'.$users->full_name.'"  data-job="'.$users->job_id.'"  class="applay_folder btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-folder"></i> Folder</a>
                      <a data-application_id="'.$users->job_application_id.'" data-user-id="'.$users->id.'" data-user_name="'.$users->full_name.'" class="applay_shortlist btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-sort-amount-desc"></i> Shortlist</a>
                      ';

                      if($users->app_rating <= 0)
                      {
                      $html .= '<a data-application_id="'.$users->job_application_id.'" data-user_name="'.$users->full_name.'"  class="apply_rating btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-thumbs-o-up"></i> Rating</a>';
                      }

                      $html .= '<a data-user="'.$users->id.'" data-job_id="'.$users->job_id.'" data-user_name="'.$users->full_name.'" class="apply_message btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-folder"></i> Message</a>
                      </div>';

                      if($users->is_short == 1)
                      {
                      $html .= '<span class="label label-success pull-right"> Short Listed</span>';
                      }

                      if($users->app_rating > 0)
                      {
                      $html .= '<span class="label label-info pull-right"> Rating</span>';
                      }

                     */

                     

                    $html .= '<a href="/resume/' . $users->id . '" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i>View</a>
                              <a id="folder_'.$users->id.'" data-user_name="' . $users->full_name . '" data-user="' . $users->id . '" class="applay_folder btn btn-primary btn-xs" href="javascript:void(0);"><i class="fa fa-folder-open"></i>Update Folder</a>
                              </div>';

                    return $html;




                    //return '<a href="/admin/seekeredit/' . $users->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                })
                ->removeColumn('id')
                ->make(true);


        return new JsonModel($output);
    }

    public function updateSeekerNotificationAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();

        $request = $this->getRequest();

        if ($request->isPost()) {

            $EloquentDb::table('chk_notification')
                    ->where('to_user_id', $auth_users->id)
                    ->update(array(
                        'to_user_status' => 'read'
            ));
        }

        $output = array();
        return new JsonModel($output);
    }

    public function expirejobAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $EloquentDb::table('chk_jobs')
                ->where('job_deadline', '<', date('Y-m-d'))
                ->update(array(
                    'status' => 0
        ));

        $output = array();
        return new JsonModel($output);
    }

    public function seekernotificationAction() {

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $result = $EloquentDb::table('chk_users')
                ->select('id', 'full_name', 'email', 'updated_at')
                ->where('user_type', '=', 3)
                ->where('updated_at', '!=', '0000-00-00 00:00:00')
                ->where('updated_at', '<', $EloquentDb::raw('DATE_SUB(NOW(), INTERVAL 2 MONTH)'))
                ->take(5)
                ->get();

        $output = array();
        return new JsonModel($output);
    }

}
