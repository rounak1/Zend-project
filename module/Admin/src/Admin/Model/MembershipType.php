<?php
namespace Admin\Model;

use Illuminate\Database\Eloquent\Model as EloquentZF2Model;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class MembershipType extends EloquentZF2Model {
    protected $table = 'chk_membership_service';
    //public $timestamps = false;    
    
    /*
    function getMembersServices ()
    {
        $results =  MembershipType::table('chk_membership_service')
        ->leftJoin('chk_users', 'chk_membership_service.created_by', '=', 'chk_users.id')
        ->select('chk_membership_service.*', 'chk_users.user_name AS userName')
        ->where('status', 1)      
        ->get(); 
        
        return $results;
    }
     * 
     */
    
}

