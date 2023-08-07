<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UserRolePermission;
use App\Models\Admin;

class UserRolePermissionController extends Controller
{   

    public function index(){
        // $a = UserRolePermission::where('role_name','superadmin')->first();
        // $s = array();

        // $m = array_map(function($x){
        //     if(strtok($x,'-') == 'enabled'){
        //         return $x;
        //     }
        // },$a->permissions);
        // dd(array_filter($m));
            
        $data = array(
            'title' => 'User Role Permission List',
            'user_roles'    => UserRolePermission::get(),
            'edit_permission'   => '',
        );

        return view('admin.user_role_permission.all_permission')->with($data);
    }
    
    public function add(){
        $data = array(
            'title' => 'Add user role permissions',
            'roles'    => UserRolePermission::get(),
        );
        return view('admin.user_role_permission.add_permission')->with($data);
    }

    public function store(Request $req){
        // dd($req->all());
        $rules = [
            'role_name'     => ['required', 'string'],
            'permissions'   => ['array', 'nullable'],
        ];
        // dd($req->permissions);
        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $validated = $validator->validated();
        
        if(isset($req->permission_id) && !empty($req->permission_id)){
            $permission = UserRolePermission::find(hashids_decode($req->permission_id));
            $msg = 'Permissions Updated Successfully';
        }else{
            if(UserRolePermission::where('role_name',$validated['role_name'])->doesntExist()){
                $permission = new UserRolePermission;
            }else{
                $permission = UserRolePermission::where('role_name',$validated['role_name'])->firstOrFail();
            }

            $msg = 'Permission Added Successfully';
        }

        $permission->role_name =    $validated['role_name'];
        $permission->permissions = @$validated['permissions'];
        $permission->save();

        if($validated['role_name'] == 'limited'){
            Admin::where('limited', 1)->update(['user_permissions'=>@$validated['permissions']]);
        }else{
            Admin::where('user_type',$validated['role_name'])->update(['user_permissions'=>@$validated['permissions']]);
        }

        return response()->json([
            'success'   => 'Permission set successfully',
            'reload'    => TRUE
        ]);

    }

    public function edit($id){
        if(isset($id) && !empty($id)){
            $data = array(
                'title' => 'Edit user role permissions',
                'edit_permission'   => UserRolePermission::where('id',hashids_decode($id))->first(),
                'roles'    => UserRolePermission::get(),
                'update'    => TRUE
            );
            
            return view('admin.user_role_permission.add_permission')->with($data);
        }
    }
}
