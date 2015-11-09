<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
// datatables 
//use yajra\Datatables\DatatablesServiceProvider;
use yajra\Datatables\Datatables as Datatables;
use Zend\Paginator\Paginator;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\StorageInterface;

class ReportsController extends AbstractActionController {

    public function spotlightJobAction() {

        $itemsPerPage = 25;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;

        unset($allGetValues['page']);

        if (count($post_data) > 0) {
            $allGetValues = array();
            unset($post_data['search_btn']);
            $all_request_data = $post_data;

            // $allGetValues  = $post_data;
        } else {
            $all_request_data = $allGetValues;
        }

        //echo "<pre>";
        // print_r($allGetValues);
        //exit;

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }

        $result = $EloquentDb::table('chk_jobs')
                ->select('chk_jobs.*', 'chk_users.full_name')
                ->leftJoin('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                ->where('chk_jobs.spot_light_used', 1)
                ->orderBy('chk_jobs.posting_date', 'DESC');

        
        foreach ($all_request_data as $key => $search_data) {

            switch ($key) {
                case "from_date":
                    if (!empty($search_data)) {
                        $result->where('posting_date', '>=', $search_data);
                    }
                    break;

                case "to_date":
                    if (!empty($search_data)) {
                        $result->where('posting_date', '<=', $search_data);
                    }
                    break;
            }
        }

        /* $offset = ($page - 1) * $itemsPerPage;
          $records =$result->take($itemsPerPage)->skip($offset)->get() ;

          $queries =  $EloquentDb::getQueryLog();
          $last_query = end($queries);
          print_r($last_query);

          echo "<pre>";
          print_r($records);
          exit(); */

        $padapter = new \Application\Paginator\Adapter($result);
        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);


        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'currentpage' => $page,
            'search_data' => $all_request_data
        ));
    }

    public function govtJobAction() {
        $itemsPerPage = 25;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 0;

        unset($allGetValues['page']);
        if (count($post_data) > 0) {
            $allGetValues = array();
            unset($post_data['search_btn']);
            $all_request_data = $post_data;

            // $allGetValues  = $post_data;
        } else {
            $all_request_data = $allGetValues;
        }

        //echo "<pre>";
        // print_r($allGetValues);
        //exit;

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }
        
        //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id')
                ->where('type', '=', 'Govjob')
                ->first();

        $result = $EloquentDb::table('chk_jobs')
                ->select('chk_jobs.*')
                ->join('chk_job_circular_type', 'chk_job_circular_type.job_id', '=',  'chk_jobs.id')
                ->where('chk_job_circular_type.job_circular_id', '=', $job_circular['id'])
                ->orderBy('chk_jobs.posting_date', 'DESC');

        foreach ($all_request_data as $key => $search_data) {

            switch ($key) {
                case "from_date":
                    if (!empty($search_data)) {
                        $result->where('chk_jobs.posting_date', '>=', $search_data);
                    }
                    break;

                case "to_date":
                    if (!empty($search_data)) {
                        $result->where('chk_jobs.posting_date', '<=', $search_data);
                    }
                    break;
            }
        }



        //->get();

        $padapter = new \Application\Paginator\Adapter($result);
        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);


        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'currentpage' => $page,
            'search_data' => $all_request_data
        ));
    }

    public function newsJobAction() {
        $itemsPerPage = 25;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 0;

        unset($allGetValues['page']);
        if (count($post_data) > 0) {
            $allGetValues = array();
            unset($post_data['search_btn']);
            $all_request_data = $post_data;

            // $allGetValues  = $post_data;
        } else {
            $all_request_data = $allGetValues;
        }

        //echo "<pre>";
        // print_r($allGetValues);
        //exit;

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }
        
         //Job job circular
        $job_circular = $EloquentDb::table('chk_job_circular')
                ->select('id')
                ->where('type', '=', 'Newspaperjob')
                ->first();

        $result = $EloquentDb::table('chk_jobs')
                ->join('chk_job_circular_type', 'chk_job_circular_type.job_id', '=', 'chk_jobs.id')
                ->select('chk_jobs.*')
                ->where('chk_job_circular_type.job_circular_id', '=', $job_circular['id'])
                ->orderBy('chk_jobs.posting_date', 'DESC');

        foreach ($all_request_data as $key => $search_data) {

            switch ($key) {
                case "from_date":
                    if (!empty($search_data)) {
                        $result->where('chk_jobs.posting_date', '>=', $search_data);
                    }
                    break;

                case "to_date":
                    if (!empty($search_data)) {
                        $result->where('chk_jobs.posting_date', '<=', $search_data);
                    }
                    break;
            }
        }



        //->get();

        $padapter = new \Application\Paginator\Adapter($result);
        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);


        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'currentpage' => $page,
            'search_data' => $all_request_data
        ));
    }

    public function activeJobAction() {
        $itemsPerPage = 25;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;

        unset($allGetValues['page']);
        if (count($post_data) > 0) {
            $allGetValues = array();
            unset($post_data['search_btn']);
            $all_request_data = $post_data;

            // $allGetValues  = $post_data;
        } else {
            $all_request_data = $allGetValues;
        }

        //echo "<pre>";
        // print_r($allGetValues);
        //exit;

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }

        $result = $EloquentDb::table('chk_jobs')
                ->select('chk_jobs.id', 'chk_jobs.employer_id', 'chk_jobs.job_title', 'chk_users.full_name', 'chk_jobs.posting_date', 'chk_jobs.job_deadline', 'chk_jobs.status'
                )
                ->join('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                ->where('chk_jobs.job_deadline', '>=', date('Y-m-d'))
                //->where('chk_jobs.job_circular_type', '=', '')
                ->where('chk_jobs.status', '=', '1')
                ->where('chk_jobs.posting_date', '<=', date("Y-m-d"))
                ->orderBy('chk_jobs.posting_date', 'DESC');

        foreach ($all_request_data as $key => $search_data) {

            switch ($key) {
                case "from_date":
                    if (!empty($search_data)) {
                        $result->where('posting_date', '>=', $search_data);
                    }
                    break;

                case "to_date":
                    if (!empty($search_data)) {
                        $result->where('posting_date', '<=', $search_data);
                    }
                    break;
            }
        }

        /* $offset = ($page - 1) * $itemCountPerPage;
          $records =$result->take($itemCountPerPage)->skip($offset)->get() ;

          $queries =  $EloquentDb::getQueryLog();
          $last_query = end($queries);
          print_r($last_query);

          echo "<pre>";
          print_r($records);
          exit(); */

        $padapter = new \Application\Paginator\Adapter($result);
        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);


        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'currentpage' => $page,
            'search_data' => $all_request_data
        ));
    }

    public function activeMembersAction() {
        $itemsPerPage = 25;
        $allGetValues = array();
        $post_data = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');


        $allGetValues = $this->params()->fromQuery();

        $post_data = $this->getRequest()->getPost()->toArray();

        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;
        unset($allGetValues['page']);

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

        $result = $EloquentDb::table('chk_users')
                ->select('id', 'full_name', 'email', 'created_at', 'user_status'
                )
                ->where('user_type', '=', '3')
                ->where('trashed', '=', '0')
                ->orderBy('updated_at', 'DESC');

        foreach ($all_request_data as $key => $search_data) {

            switch ($key) {
                case "from_date":
                    if (!empty($search_data)) {
                        $result->where('created_at', '>=', $search_data);
                    }
                    break;

                case "to_date":
                    if (!empty($search_data)) {
                        $result->where('created_at', '<=', $search_data);
                    }
                    break;
            }
        }

        /* $offset = ($page - 1) * $itemsPerPage;
          $records =$result->take($itemsPerPage)->skip($offset)->get() ;

          $queries =  $EloquentDb::getQueryLog();
          $last_query = end($queries);
          print_r($last_query);

          echo "<pre>";
          print_r($records);
          exit(); */

        /* $cache = StorageFactory::adapterFactory(
          'filesystem', array(
          'cache_dir' => '/tmp',
          'ttl' => 3600,
          'plugins' => array('serializer'),
          )); */

        $cache = StorageFactory::factory(array(
                    'adapter' => array(
                        'name' => 'filesystem',
                        'options' => array(
                            'ttl' => 30,
                            'cache_dir' =>  'data/cache',
                        ),
                    ),
                    'plugins' => array('serializer'),
        ));
        Paginator::setCache($cache);


        Paginator::setCache($cache);


        $padapter = new \Application\Paginator\Adapter($result);

        $paginator = new Paginator($padapter);

        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage);

        $total_item_count = $padapter->count();

        return new ViewModel(array(
            'paginator' => $paginator,
            'params' => $allGetValues,
            'currentpage' => $page,
            'search_data' => $all_request_data,
            'total_item_count' => $total_item_count
        ));
    }

    public function downloadcsvAction() {

        $itemsPerPage = 25;
        $allGetValues = array();
        $all_request_data = array();

        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');

        $allGetValues = $this->params()->fromQuery();
        $page = (!empty($allGetValues['page'])) ? $allGetValues['page'] : 1;
        $type = (!empty($allGetValues['type'])) ? $allGetValues['type'] : 0;
        $all_request_data = $allGetValues;

        foreach ($all_request_data as $key => $value) {
            if (!empty($value)) {
                $allGetValues[$key] = $value;
            }
        }


        switch ($type) {

            case "spotlightjob":

                $result = $EloquentDb::table('chk_jobs')
                        ->select(
                                'chk_jobs.job_title', 'chk_jobs.job_company_name', 'chk_jobs.posting_date', 'chk_jobs.job_deadline'
                        )
                        ->leftJoin('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                        ->where('chk_jobs.spot_light_used', 1)
                        ->orderBy('chk_jobs.posting_date', 'DESC');

                foreach ($all_request_data as $key => $search_data) {

                    switch ($key) {
                        case "from_date":
                            if (!empty($search_data)) {
                                $result->where('chk_jobs.posting_date', '>=', $search_data);
                            }
                            break;

                        case "to_date":
                            if (!empty($search_data)) {
                                $result->where('chk_jobs.posting_date', '<=', $search_data);
                            }
                            break;
                    }
                }

                $header = array(
                    'Job Title',
                    'Company',
                    'Posting Date',
                    'Deadline',
                );

                break;

            case "governmentjob":

                $result = $EloquentDb::table('chk_jobs')
                        ->select('job_title', 'job_company_name', 'posting_date', 'job_deadline')
                        ->where('job_circular_type', 'LIKE', '%2%')
                        ->orderBy('chk_jobs.posting_date', 'DESC');

                foreach ($all_request_data as $key => $search_data) {

                    switch ($key) {
                        case "from_date":
                            if (!empty($search_data)) {
                                $result->where('posting_date', '>=', $search_data);
                            }
                            break;

                        case "to_date":
                            if (!empty($search_data)) {
                                $result->where('posting_date', '<=', $search_data);
                            }
                            break;
                    }
                }

                $header = array(
                    'Job Title',
                    'Company',
                    'Posting Date',
                    'Deadline',
                );

                break;

            case "newspaperjob":

                $result = $EloquentDb::table('chk_jobs')
                        ->select('job_title', 'job_company_name', 'posting_date', 'job_deadline')
                        ->where('job_circular_type', 'LIKE', '%1%')
                        ->orderBy('chk_jobs.posting_date', 'DESC');

                foreach ($all_request_data as $key => $search_data) {

                    switch ($key) {
                        case "from_date":
                            if (!empty($search_data)) {
                                $result->where('posting_date', '>=', $search_data);
                            }
                            break;

                        case "to_date":
                            if (!empty($search_data)) {
                                $result->where('job_deadline', '<=', $search_data);
                            }
                            break;
                    }
                }

                $header = array(
                    'Job Title',
                    'Company',
                    'Posting Date',
                    'Deadline',
                );

                break;

            case "activejob":

                $result = $EloquentDb::table('chk_jobs')
                        ->select('chk_jobs.job_title', 'chk_users.full_name', 'chk_jobs.posting_date', 'chk_jobs.job_deadline')
                        ->join('chk_users', 'chk_users.id', '=', 'chk_jobs.employer_id')
                        ->where('chk_jobs.job_deadline', '>=', date('Y-mm-dd'))
                        //->where('chk_jobs.job_circular_type', '=', '')
                        ->where('chk_jobs.status', '=', '1')
                        ->orderBy('chk_jobs.posting_date', 'DESC');

                foreach ($all_request_data as $key => $search_data) {

                    switch ($key) {
                        case "from_date":
                            if (!empty($search_data)) {
                                $result->where('job_deadline', '>=', $search_data);
                            }
                            break;

                        case "to_date":
                            if (!empty($search_data)) {
                                $result->where('job_deadline', '<=', $search_data);
                            }
                            break;
                    }
                }

                $header = array(
                    'Job Title',
                    'Company',
                    'Posting Date',
                    'Deadline',
                );

                break;

            case "member":

                $result = $EloquentDb::table('chk_users')
                        ->select('full_name', 'email', 'created_at', 'user_status'
                        )
                        ->where('user_type', '=', '3')
                        ->where('trashed', '=', '0')
                        ->orderBy('id', 'DESC');

                foreach ($all_request_data as $key => $search_data) {

                    switch ($key) {
                        case "from_date":
                            if (!empty($search_data)) {
                                $result->where('created_at', '>=', $search_data);
                            }
                            break;

                        case "to_date":
                            if (!empty($search_data)) {
                                $result->where('created_at', '<=', $search_data);
                            }
                            break;
                    }
                }

                $header = array(
                    'Full name',
                    'Email',
                    'Joining date ',
                    'Status',
                );

                break;
        }


        $offset = ($page - 1) * $itemsPerPage;
        $records = $result->take($itemsPerPage)->skip($offset)->get();

        //$queries = $EloquentDb::getQueryLog();
        //$last_query = end($queries);
        //print_r($last_query);
        //exit;



        /* $records = array(
          array(
          '1997',
          'Ford',
          'E350',
          'ac, abs, moon',
          '3000.00',
          ),
          ); */
        $htmlViewPart = new ViewModel();
        $htmlViewPart->setTerminal(true);

        return $this->csvExport('foo.csv', $header, $records);
    }

}
