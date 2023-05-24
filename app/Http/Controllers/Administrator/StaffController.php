<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Isp;
use App\Models\City;
use App\Models\Admin;
use App\Models\Permission;  
use App\Models\ActivityLog;
use App\Models\UserRolePermission;

class StaffController extends Controller
{   
    public function index(){
        
        if(\CommonHelpers::rights('enabled-staff','enabled-staff')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title'     => 'All Staff',
            'admins'    => Admin::where('id','!=',auth()->user()->id)->whereNotIn('user_type', ['superadmin', 'admin'])->orderBy('id', 'ASC')->get(),
        );        
        return view('admin.staff.all_admins')->with($data);
    }
    
    public function add(){
        
        if(\CommonHelpers::rights('enabled-staff','add-staff')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title' => 'Add Staff',
            'roles' => UserRolePermission::whereNotIn('role_name', ['superadmin','admin'])->get(['id', 'role_name']),
        );

        return view('admin.staff.add_admin')->with($data);
    }

    public function store(Request $req){

        $rules = [
            'name'      => ['required', 'string', 'max:50'],
            'username'  => ['required', 'string', 'min:3', 'max:10', Rule::unique('admins')->ignore(@hashids_decode($req->admin_id))],
            'password'  => [Rule::requiredIf(!isset($req->admin_id)), 'nullable', 'min:6', 'max:50', 'confirmed'],
            'email'     => ['required', 'string', 'max:191', Rule::unique('admins')->ignore(@hashids_decode($req->admin_id))],
            'nic'       => ['required', 'string', 'min:15', 'max:15'],
            'mobile'    => ['required', 'numeric', 'digits:10'],
            'address'   => ['required', 'string' ],
            'nic_front' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'nic_back'  => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'image'     => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'user_type' => ['required', 'max:50']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        
        if(isset($req->admin_id)){
            $admin = Admin::findOrFail(hashids_decode($req->admin_id));
            $msg   = 'Staff Updated Successfully';
            $activity = "edited admin-($req->username)";
        }else{
            $admin = new Admin;
            $msg   = 'Staff Added Successfully';
            $activity = "added admin-($req->username)";
        }


        //nic front
        if($req->hasFile('nic_front')){
            $nic_front = \CommonHelpers::uploadSingleFile($req->nic_front, 'admin_uploads/nic_front/');
            $admin->nic_front = $nic_front;
        }
        //nic back
        if($req->hasFile('nic_back')){
            $nic_back = \CommonHelpers::uploadSingleFile($req->nic_back, 'admin_uploads/nic_back/');
            $admin->nic_back = $nic_back;
        }
        //image
        if($req->hasFile('image')){
            $image = \CommonHelpers::uploadSingleFile($req->image, 'admin_uploads/images/');
            $admin->image = $image;
        }

        // $admin->edit_by_id  = auth()->user()->id;
        $admin->name        = $req->name;
        $admin->username    = $req->username;
        $admin->password    = Hash::make(@$req->password);
        $admin->email       = $req->email;
        $admin->nic         = $req->nic;
        $admin->mobile      = '92'.$req->mobile;
        $admin->address     = $req->address;

        $permissions = UserRolePermission::where('role_name',$req->user_type)->first();

        $admin->user_permissions = $permissions->permissions;
        $admin->user_type   = $permissions->role_name;
        $admin->save();

        \CommonHelpers::activity_logs($activity);

        return response()->json([
            'success'     => $msg,
            'redirect'    => route('admin.staffs.index'),
        ]);
    }

    //edit admin
    public function edit($id){
        
        if(\CommonHelpers::rights('enabled-staff','edit-staff')){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data = array(
                'title'         => 'Edit Admin',
                'edit_admin'    => Admin::findOrFail(hashids_decode($id)),
                'roles' => UserRolePermission::whereNotIn('role_name', ['superadmin','admin'])->get(['id', 'role_name']),
                'is_update'     => TRUE
            );
            \CommonHelpers::activity_logs('edit-staff');
            
            return view('admin.staff.add_admin')->with($data);
        }
        abort(404);
    }
    //disaply details in modal
    public function details($id){
        if(\CommonHelpers::rights('enabled-staff', 'enabled-staff')){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data  = array(
                'title'         => 'Admin Profile',
                'admin_details' => Admin::findOrFail(hashids_decode($id)),
                'activity_logs' => ActivityLog::where('user_id',hashids_decode($id))->latest()->paginate(10),
            );

            return view('admin.staff.admin_profile')->with($data);
        }
        abort(404);
    }

    //chheck unique value of specified column
    public function checkUnique(Request $req){

        if(isset($req->column) && isset($req->value) && !empty($req->column) && !empty($req->value)){
            
            $query = Admin::when(isset($req->column),function($q) use ($req){
                $q->where($req->column,$req->value);
            })->when(isset($req->id),function($q) use ($req){
                $q->where('id','!=',hashids_decode($req->id));
            })->first();

            if(!empty($query)){
                echo 'false';
                die();
            }
        }
        echo 'true';
        die();

    }

    //remove attachments only for admins
    public function removeAttachment(Request $req){
        
        if(\CommonHelpers::rights('enabled-staff','update-staff-documents')){
            return redirect()->route('admin.home');
        }

        if(isset($req->id) && isset($req->type) && isset($req->path)){
            
            $remove = Admin::findOrFail(hashids_decode($req->id));

            if($req->type == 'nic_front'){
                $remove->nic_front = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'nic_back'){
                $remove->nic_back = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'user_form_front'){
                $remove->user_form_front = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'user_form_back'){
                $remove->user_form_back = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'image'){
                $remove->image = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'agreement'){
                $remove->image = null;
                @unlink(public_path($req->path));
            }

            $remove->save();

            // \CommonHelpers::activity_logs('remoev-attachment');
            
            return response()->json([
                'success'   => 'Attachment Removed Successfully',
                'reload'    => TRUE
            ]);
        }
        abort(404);
    }

    //update admin documents
    public function updateDocument(Request $req){
        
        if(\CommonHelpers::rights('enabled-staff','update-staff-documents')){
            return redirect()->route('admin.home');
        }
        
        if(isset($req->user_id) && !empty($req->user_id)){

            $rules = [
                'nic_front'         => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
                'nic_back'          => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
                'image'             => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],            ];

            $validator = Validator::make($req->all(),$rules);

            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }

            $user = Admin::findOrFail(hashids_decode($req->user_id));
            
            //nic front
            if($req->hasFile('nic_front')){
                $nic_front = \CommonHelpers::uploadSingleFile($req->nic_front, 'admin_uploads/nic_front/');
                $user->nic_front = $nic_front;
                @unlink($req->old_nic_front);
            }
            //nic back
            if($req->hasFile('nic_back')){
                $nic_back = \CommonHelpers::uploadSingleFile($req->nic_back, 'admin_uploads/nic_back/');
                $user->nic_back = $nic_back;
                @unlink($req->old_nic_back);
            }
            //admin image
            if($req->hasFile('image')){
                $image = \CommonHelpers::uploadSingleFile($req->image, 'admin_uploads/user_form_front/');
                $user->image = $image;
                @unlink($req->old_image);
            }

            $user->save();
            
            // \CommonHelpers::activity_logs('update-documents');

            return response()->json([
                'success'   => 'Document Udpated Successfully',
                'reload'    => True
            ]);
        }    
    }

    //update admin password
    public function updatePassword(Request $req){
        
        if(\CommonHelpers::rights('enabled-staff','edit-staff')){
            return redirect()->route('admin.home');
        }

        $rules = [
            'password'  => ['required', 'min:6', 'max:12', 'confirmed'],
            'admin_id'   => ['required']
        ];

        $validator = Validator::make($req->all(),$rules);
        $validated = $validator->validated();

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $admin = Admin::findOrFail(hashids_decode($validated['admin_id']));
        $admin->password = Hash::make($validated['password']);
        $admin->save();

        \CommonHelpers::activity_logs("changed password-($admin->username)");

        return response()->json([
            'success'   => 'User password Updated Successfully',
            'redirect'    => route('admin.users.profile',['id'=>$validated['admin_id']])
        ]);
    }

    //update admin personal info
    public function updateInfo(Request $req){
        
        if(\CommonHelpers::rights('enabled-staff','edit-staff')){
            return redirect()->route('admin.home');
        }

        if(isset($req->admin_id) && !empty($req->admin_id)){
            $rules = [
                'name'              => ['required', 'string', 'max:50'],
                'nic'               => ['required', 'string', 'min:15', 'max:15'],
                'mobile'            => ['required', 'numeric', 'digits:10'],
                'address'           => ['required', 'string' ],
                'admin_id'          => ['required']
            ];
      
            $validator = Validator::make($req->all(),$rules);

            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }
            
            
            $admin       = Admin::findOrFail(hashids_decode($req->admin_id));
            $msg        = 'Admin Personal Info Updated Successfully';
            $activity   = 'updated-personal-info';
            
            $admin->name        = $req->name;
            $admin->nic         = $req->nic;
            $admin->mobile      = '92'.$req->mobile;
            $admin->address     = $req->address;
            $admin->save();

            \CommonHelpers::activity_logs($activity);

            return response()->json([
                'success'   => $msg,
                'redirect'    => route('admin.admins.index'),
            ]);
        }
        abort(404);
    }

    public function delete($id){
        
        if(\CommonHelpers::rights('enabled-staff','delete-staff')){
            return redirect()->route('admin.home');
        }

        Admin::destroy(hashids_decode($id));
        return response()->json([
            'success'   => 'Staff deleted succcessfully',
            'reload'    => true
        ]);
    }
}
