<?php

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RoleController extends AbstractActionController
{

    public function indexAction()
    {
        $results = array();
        $EloquentDb = $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
        
        $chk_roles =  $EloquentDb::table('chk_roles')
                        ->select('chk_roles.id', 'chk_roles.created_at','chk_roles.role_name', 'chk_permissions.perm_name')
                        ->leftJoin('chk_role_permissions','chk_role_permissions.role_id','=', 'chk_roles.id')
                        ->leftJoin('chk_permissions','chk_permissions.id','=', 'chk_role_permissions.perm_id')                           
                        ->get();
        
        //echo "<pre>";
        //print_r($chk_roles);
        //exit;
        
        
        foreach($chk_roles as $row)
        {
            $results[$row['id']]['id']         = $row["id"];
            $results[$row['id']]['role_name']  = $row["role_name"];
            $results[$row['id']]['created_at'] = $row["created_at"];
            
            $role_name = $row["role_name"];
            $results[$row['id']][$role_name][] = $row["perm_name"];
        }
        
        //echo "<pre>";
        //print_r($results);
        //exit;
                       
        return new ViewModel(array('results' => $results));
    }
    
    public function addroleAction() {        
        
        $selected_role_permissions_id = array();
        
        $request    = $this->getRequest();
        $post_data  = $request->getPost();
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $permissions = $EloquentDb::table('chk_permissions')
                ->select('id','perm_name')                
                ->get();
        
        if ($request->isPost()) {      
            $chk_roles = [
                            'role_name'  => $request->getPost('role_name'),
                            'created_by' => $auth_users->id,
                            'created_at' => date("Y-m-d H:i:s") 
                         ];
            
            $role_id = $EloquentDb::table('chk_roles')->insertGetId( $chk_roles );
            
            if(count($request->getPost('perm_id')) > 0 )
            {               
                foreach($request->getPost('perm_id') as $perm_id)
                {
                    $EloquentDb::table('chk_role_permissions')->insert(
                                        [
                                            'role_id' => $role_id, 
                                            'perm_id' => $perm_id
                                        ]
                                    );
                }    
            }
            
            $this->flashMessenger()->setNamespace('success')->addMessage('Role been added.');
            return $this->redirect()->toUrl("/admin/role");                         
        }

        return new ViewModel(array(          
            'post_data' => $post_data,
            'permissions' => $permissions,
            'selected_role_permissions_id' => $selected_role_permissions_id
        ));
    }
    
    public function editroleAction() {        
        
        $selected_role_permissions_id = array();
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $results = $EloquentDb::table('chk_roles')
                ->select('id','role_name')
                ->where('id', $id)
                ->first();
        
        $permissions = $EloquentDb::table('chk_permissions')
                ->select('id','perm_name')                
                ->get();
        
        //Selected permission
        $chk_roles =  $EloquentDb::table('chk_role_permissions')
                        ->select('chk_role_permissions.perm_id')                        
                        ->where('chk_role_permissions.role_id', $id)        
                        ->get();
        
        foreach($chk_roles as $val)
        {
            $selected_role_permissions_id[] = $val["perm_id"];
        }
        
        //echo "<pre>";
        //print_r($selected_role_permissions_id);
        //exit;                
        
        
        if ($request->isPost()) {        
            
            $EloquentDb::table('chk_roles')
            ->where('id', $id)
            ->update(array(                        
                        'role_name'   => $request->getPost('role_name'),
                        'modified_by' => $auth_users->id,
                        'updated_at'  => date("Y-m-d H:i:s")
                    ));
            
            
            //Update Role permission
            if(count($request->getPost('perm_id')) > 0 )
            {
                $EloquentDb::table('chk_role_permissions')->where('role_id', $id)->delete();
                
                foreach($request->getPost('perm_id') as $perm_id)
                {
                    $EloquentDb::table('chk_role_permissions')->insert(
                                        [
                                            'role_id' => $id, 
                                            'perm_id' => $perm_id
                                        ]
                                    );
                }    
            }
            
            $this->flashMessenger()->setNamespace('success')->addMessage('Role Permission Updated.');
            return $this->redirect()->toUrl("/admin/role");          
            
        } else {
            
        }

        $view = new ViewModel(array(                                
                                'results'     => $results,                                
                                'post_data'   => $post_data,
                                'permissions' => $permissions,
                                'selected_role_permissions_id' => $selected_role_permissions_id
                            ));

        $view->setTemplate('admin/role/addrole.phtml');
        return $view;
    }
    
    public function permissionsAction()
    {
        $EloquentDb = $this->getServiceLocator()
                    ->get('EloquentZF2Adapter');
        
        $results = $EloquentDb::table('chk_permissions')
                ->select('*')
                ->orderBy('updated_at','DESC')
                ->get();
                       
        return new ViewModel(array('results' => $results));
    }
    
    public function addpermissionAction() {        
        
        $request    = $this->getRequest();
        $post_data  = $request->getPost();
        $auth_users = $this->getServiceLocator()->get('AuthService')->getStorage()->read();
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        if ($request->isPost()) {      
            
            $EloquentDb::table('chk_permissions')->insert(
                            [
                                'perm_name'  => $request->getPost('perm_name'),
                                'created_by' => $auth_users->id,
                                'created_at' => date("Y-m-d H:i:s")
                            ]
                    );

            $this->flashMessenger()->setNamespace('success')->addMessage('Permission been added.');
            return $this->redirect()->toUrl("/admin/role/permissions");                         
        }

        return new ViewModel(array(          
            'post_data' => $post_data
        ));

        //return new ViewModel();
    }
    
    public function editpermissionAction() {
        
        $request = $this->getRequest();
        $id      = $this->params()->fromRoute('id');
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        if ($request->isPost()) {        
            
            $EloquentDb::table('chk_permissions')
            ->where('id', $id)
            ->update(array(                        
                        'perm_name'   => $request->getPost('perm_name'),
                        'modified_by' => $auth_users->id,
                        'updated_at'  => date("Y-m-d H:i:s")
                    ));
            
            $this->flashMessenger()->setNamespace('success')->addMessage('Permissions Updated.');
            return $this->redirect()->toUrl("/admin/role/permissions");         
            
        } else {
            $results =  $EloquentDb::table('chk_permissions')
                        ->select('perm_name')
                        ->where('id', $id)        
                        ->first();
        }

        $view = new ViewModel(array(                                
                                'results'   => $results,                                
                                'post_data' => $post_data
                            ));

        $view->setTemplate('admin/role/addpermission.phtml');
        return $view;
    }
    
    public function deletepermissionAction() {
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        
        $EloquentDb = $this->getServiceLocator()
                ->get('EloquentZF2Adapter');
        
        $EloquentDb::table('chk_permissions')->where('id', $id)->delete();        

        $this->flashMessenger()->setNamespace('success')->addMessage('Permission has been deleted.');
        return $this->redirect()->toUrl("/admin/role/permissions");  
    }
}

