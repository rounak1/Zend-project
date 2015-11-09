<?php
namespace Admin\Model;

use Chkuser\Model\User as User;
use Illuminate\Database\Eloquent\Model as EloquentZF2Model;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class General extends EloquentZF2Model {
    //protected $table = 'chk_business_type';
    
    public function inrementJob($EloquentDb, $values, $tableName='chk_job_category')
    {   
              
       foreach ($values as $id)
       { 
            $EloquentDb::table($tableName)->where('id', '=', $id)->increment('TotalJobs');
       }
        
    }
    
    public function decrementJob($EloquentDb, $values, $tableName='chk_job_category')
    {   
              
       foreach ($values as $id)
       { 
            $EloquentDb::table($tableName)->where('id', '=', $id)->decrement('TotalJobs');
       }
        
    }
    
    public function companyTotalJobOpening($EloquentDb, $company_id){
        
       $company_job_count = $EloquentDb::table('chk_jobs')
                ->where('employer_id', '=', $company_id)
                ->where('status', '=', "1")
                ->where('job_deadline', '>=', date("Y-m-d"))
                ->count();
       
       return $company_job_count;
        
    }
    
    public function jobView($EloquentDb, $job_id)
    {
       $EloquentDb::table('chk_jobs')->where('id', '=', $job_id)->increment('view');        
    }
}