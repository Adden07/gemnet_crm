<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\QtOver;
use Illuminate\Http\Request;
use App\Models\RadCheck;
use App\Models\RadUserGroup;
use App\Models\User;

class CronController extends Controller
{   
    // public function __construct(){//only supeadmin can access this
    //     $this->middleware(function ($request, $next) {
    //         if(auth()->user()->user_type != 'superadmin'){
    //             return redirect()->route('admin.login');
    //         }
    //         return $next($request);
    //     });
    // }
    public function userExpiry(){
        // dd('done');
        // $users = RadCheck::where('attribute','Expiration')->whereDate('value','>',date('Y-m-d'))->dd();
        // $users = RadCheck::select(\DB::raw("username,STR_TO_DATE(value,'%d-%d-%Y') as date,value"))->where('attribute','Expiration')->where('value', '<',date('Y-m-d'))->get();
        // $now = date('d-M-Y');
        // $d = \DB::raw("SELECT * FROM radcheck WHERE `attribute`='Expiration' AND str_to_date(`value`,'%d-%m-%Y') > $now");
        // $d = \DB::table('radcheck')->select(\DB::raw("*,str_to_date(`value`,'%d-%m-%Y') AS date"))->where('attribute','Expiration')->get();
        // dd($d);
        // $now = date('d M Y 12.00');
        $now    = date('Y-m-d');
        
        $now    = (date('H') == '00') ? $now.' 00:00' : $now.' 12:00';
        // dd($now);
        $users  = RadCheck::where('attribute','Expiration')
                       ->whereNotNUll('value')
                       ->whereRaw("STR_TO_DATE(`value`, '%d %M %Y %H:%i') <= '$now'")
                       ->get();//get the expired user's usernames
        // dd($users);
        $usernames = $users->pluck('username')->toArray();//convert to array
        $count     = User::whereIn('username',$usernames)->where('status','!=','expired')->update(['status'=>'expired']);//expire user status

        return "$count Users Expired Successfully";
    }

    public function resetQouta(){
        // $user = User::with(['primary_package'])->findOrFail(hashids_decode($id));//find user
        // $user_qt_expired = $user->qt_expired;
        // if($user->qt_expired == 1){
        //     $user->qt_expired = 0;
        //     $user->c_package  = $user->package;//replace current package with primay package
        //     //update raduesrgroup table because we have updated the package
        //     RadUserGroup::where('username',$user->username)->update(['groupname'=>$user->primary_package->groupname]);
        // }
        // $user->qt_used = 0;
        // $user->save();
        
        // if(is_null($user->last_login_time) || $user_qt_expired == 1){//kick user if user if online or qt_expired is 1
        //     CommonHelpers::kick_user_from_router($id);
        // }
        
        // CommonHelpers::activity_logs('reset-user-qouta');

        // return response()->json([
        //     'success'   => 'Qouta Rest Successfully',
        //     'reload'    => TRUE,
        // ]);
        dd('done');
    }

    public function qtOver(){
        
        $users = User::with(['current_package.default_package'])
                    ->whereColumn('qt_used', '>', 'qt_total')
                    ->where('qt_enabled', 1)
                    ->where('qt_expired', 0)
                    ->where('status', 'active')
                    ->get();//get those users whose qt_used are greater than qt_total
        
        $qt_over            = array();
        $kicked_users_count = 0;
        
        // dd($users[0]->current_package->default_package->id);
        foreach($users AS $key=>$user){
            if(isset($user->current_package->default_package->id)){
                $qt_over[$key] = array(
                    'user_id'       => $user->id,
                    'package_id'    => $user->c_package,
                    'default_pkg_id'=> $user->current_package->default_package->id,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                ); 
                //update user package to default package
                $u = User::findOrFail($user->id);
                $u->c_package = $user->current_package->default_package->id;
                $u->qt_expired = 1;
                $u->save();
                //update rad user group
                RadUserGroup::where('username',$user->username)->update(['groupname'=>$user->current_package->default_package->groupname]);
                if(CommonHelpers::kick_user_from_router(hashids_encode($user->id))){
                    $kicked_users_count += 1;
                }
            }
        }
        
        if(!empty($qt_over)){
            QtOver::insert($qt_over);
        }
        return count($qt_over)." users updated successfully and $kicked_users_count users kicked";
    }
}
