<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\City;
use App\Models\Admin;

class StaffController extends Controller
{
    public function index(){
        
        $data = array(
            'title' => 'All Admins',
            'admins'    => Admin::whereIn('user_type',['supervisor', 'sales_person', 'accounts', 'support', 'recovery' ])->paginate(10),
        );

        \CommonHelpers::activity_logs('all-staff');
        
        return view('admin.staff.all_staff')->with($data);
    }

    public function add(){
        $data = array(
            'title' => 'Add Staff',
            'cities'    => City::get(),
        );

        return view('admin.staff.add_staff')->with($data);
    }

    public function store(Request $req){
        $rules = [

            'role'      => ['required', 'string', 'in:supervisor,sales_person,accounts,support,recovery'], 
            'city_id'   => ['required'],
            'name'      => ['required', 'string', 'max:50'],
            'username'  => ['required', 'string', 'min:3', 'max:6', 'unique:admins'],
            'password'  => ['required', 'min:6', 'max:50', 'confirmed'],
            'email'     => ['required', 'string', 'max:191', 'unique:admins'],
            'nic'       => ['required', 'string', 'min:15', 'max:15'],
            'address'       => ['required', 'string']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        
        $validated = $validator->validated();
        
        $admin = new Admin;
        $admin->edit_by_id  = auth()->user()->id;
        $admin->city_id     = hashids_decode($validated['city_id']);
        $admin->name        = $validated['name'];
        $admin->username    = $validated['username'];
        $admin->password    = Hash::make($validated['password']);
        $admin->email       = $validated['email'];
        $admin->nic         = $validated['nic'];
        $admin->user_type   = $validated['role'];
        $admin->save();

        \CommonHelpers::activity_logs('added-staff');

        return response()->json([
            'success'   => 'Staff Added Successfully',
            'reload'    => TRUE
        ]);
    }
}
