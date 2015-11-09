<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Paginator;

use Zend\Paginator\Adapter\AdapterInterface;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class Jobadapter implements AdapterInterface {

    protected $repository;
    public $totalItemCount;
    public $result_found;
    
    /**
     * Construct
     *
     * @param \eloquent  $repository Repository class
     */
    public function __construct($repository) {
        $this->repository = $repository;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param int $offset           Page offset
     * @param int $itemCountPerPage Number of items per page
     *
     * @return array
     */
    public function getItems($offset, $itemCountPerPage) {

        /* $EloquentDb = $this->getController()->getServiceLocator()
          ->get('EloquentZF2Adapter'); */

        /* echo 'page : '.$page;
          echo 'count item per page : '.$itemCountPerPage;

          $offset = ($page - 1) * $itemCountPerPage;

          echo 'offset : '.$offset; */

       
        $records = $this->repository->take($itemCountPerPage)->skip($offset)->get();
       

        // $employer_records = $this->repository->WhereNotNull('employer_id')->take($itemCountPerPage)->skip($offset)->get();
        
        // $records = $employer_records;
        //$newspaper_records = $this->repository->where('job_circular_type',1)->take($itemCountPerPage)->skip($offset)->get();
        
        //$govt_records = $this->repository->take($itemCountPerPage)->skip($offset)->get();
        
        /*if ($newspaper_records)
        {
            $records = array_merge($employer_records,$newspaper_records);
        }else
        {
            $records = $employer_records;
        }*/
        
            
       
        
        
        $this->result_found = count($records);
        
        
        return $records;
    }

    /**
     * Count results
     *
     * @return int
     */
    public function count() {
        return $this->repository->count();
    }

    public function getTotalItemCount() {

           return $this->repository->get()->count();
        //return $this->totalItemCount;
    }
    
    public function getResultFound ()
    {
        echo "test";
        return $this->result_found;
    }

}

