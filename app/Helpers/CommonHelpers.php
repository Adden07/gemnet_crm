<?php

namespace App\Helpers;

use App\Models\ApiResponse;
use App\Models\Shipment;
use App\Models\UserDetails;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Radacct;
use App\Models\Nas;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use SoapClient;
use App\Models\Admin;
use Illuminate\Support\Arr;

class CommonHelpers
{
    public static function send_email($view, $data, $to, $subject = 'Welcome !', $from_email = null, $from_name = null)
    {
        $from_name = $from_name ?? config('mail.from.address');
        $from_email = $from_email ?? config('mail.from.name');
        $data['subject'] = $subject;
        $data['to'] = $to;
        $data['from_name'] = $from_name;
        $data['from_email'] = $from_email;

        $sentEmail = CommonHelpers::save_email_to_db($data, $view, $data);

        $data['email_id'] = hashids_encode($sentEmail->id);
        $data['email_data'] = $data;

        try {
            Mail::send('emails.' . $view, $data, function ($message) use ($data) {
                $message->from($data['from_email'], $data['from_name']);
                $message->subject($data['subject']);
                $message->to($data['to']);
            });
            return true;
        } catch (\Exception $ex) {
            return response()->json($ex);
        }
    }

    public static function save_email_to_db($data, $view, $email_data)
    {
        $sentEmail = new \App\Models\UsersEmail();
        $sentEmail->user_id = $data['user_id'] ?? null;
        $sentEmail->user_type = $data['user_type'] ?? null;
        $sentEmail->parent_id = $data['parent_id'] ?? null;
        $sentEmail->sender_id = $data['sender_id'] ?? null;
        $sentEmail->is_public = $data['is_public'] ?? 0;
        $sentEmail->is_notification = $data['is_notification'] ?? 1;
        $sentEmail->subject = $data['subject'];
        $sentEmail->type = $view;
        $sentEmail->data = $email_data;
        $sentEmail->save();
        return $sentEmail;
    }

    public static function pdf_file($path, $dir, $view, $name, $data)
    {
        if(Storage::has($path)){
            return Storage::download($path);
        }

        $pdf = \PDF::loadView($view, array($name => $data));
        $content = $pdf->output();

        Storage::put($path, $content, 'private');
        return Storage::download($path);
    }

    public static function uploadSingleFile($file, $path = 'uploads/images/', $types = "png,gif,jpeg,jpg", $filesize = '1000', $absolute_path = false)
    {
        if ($absolute_path == false) {
            $path = $path . date('Y');
        }

        $rules = array('image' => 'required|mimes:' . $types . "|max:" . $filesize);
        $validator = \Validator::make(array('image' => $file), $rules);
        if ($validator->passes()) {

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file_path = Storage::put($path, $file);
            return $file_path;
        } else {
            return ['error' => $validator->errors()->first('image')];
        }
    }

    public static function activity_logs($activity){
        ActivityLog::insert([
            'user_id'   => auth()->user()->id,
            'user_ip'   => request()->ip(),
            'activity'  => $activity,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }

    public static function rights($permission_type,$permission){
        if(!(auth()->user()->can($permission_type) && auth()->user()->can($permission))){
           return true;
        }
        return false;
    }

    // function kick_user_from_router($username, $activity_msg = 'Manually User Kicked'){
    //     $ci = &get_instance(); //get main CodeIgniter object
    //     $ci->load->database(); //load databse library

    //     $query = $ci->db->query("select id from usersinfo where username = '$username'");
       
    //     if ($query->num_rows() > 0) {
    //         $user_id = $sessionid = $query->row()->id;

    //         $query = $ci->db->query("select nasipaddress, acctsessionid from radacct where username = '$username' AND acctstoptime is NULL");

    //         if ($query->num_rows() > 0) {
    //             $nasipaddress = $query->row()->nasipaddress;
    //             $sessionid = $query->row()->acctsessionid;
                
    //             $query = $ci->db->query("select server, secret from nas where nasname ='$nasipaddress'");
    
    //             if ($query->num_rows() > 0) {
    //                 $nas = $query->row()->server;
    //                 $nas_secret = $query->row()->secret;
    //                 $command = "echo user-name=$username,Acct-Session-Id=$sessionid | radclient -x $nas disconnect $nas_secret";
    //                 exec($command, $output, $retval);
    //                 $ci->main->insertActivity($activity_msg, $user_id);
    //                 return true;
    //             }
    //         }
    //     }
    //     return false;
    // }

    public static function kick_user_from_router($id){

        $user               = User::findOrFail(hashids_decode($id));
        $radacct            = Radacct::where('username',$user->username)->whereNUll('acctstoptime')->first();
        $username           = $user->username;
        // dd($radacct);
        if(collect($radacct)->isNotEmpty()){

            $nasipaddress   = $radacct->nasipaddress;
            $sessionid      = $radacct->acctsessionid;

            $nas_q =          Nas::where('nasname',$nasipaddress)->first();

            if(collect($nas_q)->isNotEmpty()){
            
                $nas        = $nas_q->server;
                $nas_secret = $nas_q->secret;
                $command    = "echo user-name=$username,Acct-Session-Id=$sessionid | radclient -x $nas disconnect $nas_secret";
                
                exec($command, $output, $retval);
                
                return true;
            }
        }
        return false;
        
    }

    public static function getChildIds($frachise_id = null){//get child of login admin
        
        $ids = array();

        if($frachise_id != null){
            $arr['franchise_id']    = $frachise_id;
            $arr['dealer_ids']      = Admin::where('added_to_id',$arr['franchise_id'])->get('id')->pluck('id')->toArray();
            $arr['subdealer_ids']   = Admin::whereIn('added_to_id',$arr['dealer_ids'])->get('id')->pluck('id')->toArray();
            return Arr::flatten($arr);
        }
        //is user is franchise then get all dealers and subdealers and return their ids
        if(auth()->user()->user_type == 'franchise'){
            
            $arr = array();
            $arr['franchise_id']    = auth()->user()->id;
            $arr['dealer_ids']      = Admin::where('added_to_id',$arr['franchise_id'])->get()->pluck('id')->toArray();
            $arr['subdealer_ids']   = Admin::whereIn('added_to_id',$arr['dealer_ids'])->get()->pluck('id')->toArray();
            
            $ids = Arr::flatten($arr);//convert multi dimensional array to single array
        //if user is dealer then get all subdealers and return their ids
        }elseif(auth()->user()->user_type == 'dealer'){
            $arr = array();
            $arr['dealer_id'] = auth()->user()->id;
            $arr['subdealer_ids'] = Admin::where('added_to_id',$arr['dealer_id'])->get()->pluck('id')->toArray();

            $ids = Arr::flatten($arr);//cnvert multidimensiaonl array to single arra
        //if user is subdealer their return its own id    
        }elseif(auth()->user()->user_type == 'sub_dealer'){
            $ids[] = auth()->user()->id;
        }
        return $ids;
    }


    public static function getFranchiseNetworkIds($admin_id, $type, $unset=false){//this function get the franchise network ids of specified admin_id
        $arr = array();
        if($type == 'franchise'){
            $arr['franchise_id']    = $admin_id;
            $arr['dealer_ids']      = Admin::where('added_to_id',$arr['franchise_id'])->get('id')->pluck('id')->toArray();
            $arr['subdealer_ids']   = Admin::whereIn('added_to_id',$arr['dealer_ids'])->get('id')->pluck('id')->toArray();
            
            if($unset == false){
                unset($arr['franchise_id']);//unset franchise id so it only return child ids
            }
            
            return Arr::flatten($arr);
        
        }elseif($type == 'dealer'){
            $arr['dealer_id'] = $admin_id;
            $arr['subdealer_ids'] = Admin::where('added_to_id',$arr['dealer_id'])->get()->pluck('id')->toArray();
            
            if($unset == false){
                unset($arr['dealer_id']);//unset dealer id so it only return child ids
            }

            return  Arr::flatten($arr);//convert multidimensiaonl array to single arra
        }
        abort(404);
    }

    //limit permission
    // public static function setPermissionLimit($id, $limit){
    //     $user = Admin::findOrFail(hashids_decode($id));
    //     $user->limited = $limit;
    //     $user->save();
    // }

    // public static function kickUser($id){
    //     if(isset($id) && !empty($id)){
    //         if(CommonHelpers::kick_user_from_router($id)){
    //             $message = [
    //                 'success'   => 'User Kicked Successfully',
    //                 'reload'    => true,
    //             ];
    //         }else{
    //             $message = [
    //                 'error' => 'Something wrong',
    //             ];
    //         }
    //         return response()->json($message);
    //     }
    //     abort(404);
    // }
}
