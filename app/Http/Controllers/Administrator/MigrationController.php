<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;

class MigrationController extends Controller
{
    public function index(){

        // \DB::table('admin_2')->where('dealer',0)->update(['dealer'=>null]);
        // \DB::table('admin_2')->where('franchise',0)->update(['franchise'=>null]);
        // dd('done');
        $old_admins =\DB::table('admin_2')->get();
        $new_admins = array();
        $user_type  = null;
        $added_o_id = null;

        foreach($old_admins AS $admin){
            
            // if(is_null($admin->franchise) && is_null($admin->dealer)){//dealer and franchise is null means users is franchsie
            //     $user_type  = 'franchise';
            //     $added_o_id = null;
            // }elseif(!(is_null($admin->franchise)) && is_null($admin->dealer)){//if franchise is not null and dealer is null it means its a dealer
            //     $user_type = 'dealer';
            //     $added_o_id= $admin->franchise;
            // }elseif(!(is_null($admin->franchise)) && !(is_null($admin->dealer))){//if franchsie and dealer id is not null it means subdealer
            //     $user_type = 'sub_dealer';
            //     $added_o_id= $admin->dealer;
            // }

            if($admin->role == 11){
                $user_type  = 'franchise';
                $added_to_id = null;
            }elseif($admin->role == 12){
                $user_type  = 'dealer';
                $added_to_id = $admin->franchise;
            }elseif($admin->role == 13){
                $user_type  = 'sub_dealer';
                $added_to_id = $admin->dealer;
            }

            
            
            //convert nic 
            $nic    = str_split($admin->nic);
            $new_nic = array_merge(array_slice($nic, 0, 5), array('-'), array_slice($nic, 5));
            $new_nic = array_merge(array_slice($new_nic, 0, 13), array('-'), array_slice($new_nic, 13));

            $new_admins[] = array(
                'id'        => $admin->adminid,
                'edit_by_id'=> 2,
                'added_to_id'=> $added_to_id,
                'city_id'    => null,
                'setting_id' => null,
                'name'       => $admin->name,
                'username'   => $admin->username,
                'email'      => $admin->email,
                'password'   => \Hash::make('Gemnet#123'),
                'nic'        => implode($new_nic),
                'mobile'     => $admin->mobile,
                'nic_front'  => null,
                'nic_back'   => null,
                'agreement'  => null,
                'image'      => null,
                'address'    => $admin->address,
                'user_type'  => $user_type,
                'is_active'  => 'active',
                'credit_limit'=> 0,
                'balance'     => 0,
                'remember_token' => null,
                'user_permissions' => null,
                'last_login' => $admin->lastlogin,
                'created_at' => $admin->joindate,
                'updated_at' => null

            );
        }
        
        Admin::insert($new_admins);

        // $old_users = \DB::table('usersinfo')
        //                 ->leftJoin('radcheck_2','usersinfo.username','=','radcheck_2.username')
        //                 ->where('radcheck_2.attribute','Expiration')
        //                 ->get();
        // // dd($old_users[0]);                
        // $new_users = array();
        // // dd($old_users);
        // ini_set('max_execution_time', 99999);
        // foreach($old_users->chunk(1000) AS $user){
        //     dd($user[0]->id);
        //     //convert nic 
        //     $nic    = str_split($user->nic);
        //     $new_nic = array_merge(array_slice($nic, 0, 5), array('-'), array_slice($nic, 5));
        //     $new_nic = array_merge(array_slice($new_nic, 0, 13), array('-'), array_slice($new_nic, 13));
            
        //     $new_users[] = array(
        //         'id'    => $user->id,
        //         'admin_id'  =>$user->saleperson,
        //         'city_id'   => null,
        //         'area_id'   => null,
        //         'subarea_id'=>null,
        //         'name'      => $user->name,
        //         'username'  => $user->username,
        //         'password'  => $user->password,
        //         'portal_pass'=> null,
        //         'nic'   => implode($new_nic),
        //         'mobile'    => $user->mobile,
        //         'address'   => $user->address,
        //         'status'    => $user->user_status,
        //         'user_status' => 1,
        //         'package'   => $user->package,
        //         'c_package' => $user->c_package,
        //         'last_package'  => null,
        //         'maclock'  => 1,
        //         'macs'  => 3,
        //         'macvendor' => null,
        //         'last_macvendor'   => null,
        //         'macaddress'   => null,
        //         'qt_enabled'    => 1,
        //         'qt_expired'    => 0,
        //         'qt_total'  => $user->qt_total,
        //         'qt_used'   => $user->qt_used,
        //         'nic_front' => null,
        //         'nic_back'  => null,
        //         'user_form_front' => null,
        //         'user_form_back' => null,
        //         'created_at'    => $user->joindate,
        //         'updated_at'    => $user->updatedate,
        //         'activation_by'  => $user->createdby,
        //         'activation_date' => $user->joindate,
        //         'renew_by'  => $user->saleperson,
        //         'renew_date'   => null,
        //         'current_expiration_date'    => @date('Y-m-d 12:00:00',strtotime($user->value)),
        //         'last_expiration_date' => null,
        //         'last_profile_visit_time' => null,
        //         'last_interim_update'   => null,
        //         'last_login_time' => null,
        //         'last_logout_time' => null,
        //     );
        // }
        // dd($new_users);
        // User::insert($new_users[0]);
        
        // dd('donee');
    }
}
