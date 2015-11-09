<?php
namespace Admin\Model;

use Chkuser\Model\User as User;
use Illuminate\Database\Eloquent\Model as EloquentZF2Model;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class JobCategory extends EloquentZF2Model {
    protected $table = 'chk_job_category';
    //public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo('Chkuser\Model\User');
    }
    
    
}

