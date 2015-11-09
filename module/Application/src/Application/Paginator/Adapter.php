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

class Adapter implements AdapterInterface {

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
