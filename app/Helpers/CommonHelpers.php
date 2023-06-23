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
use App\Models\Setting;
use App\Models\Sms;
use App\Models\SmsLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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

    public static function generateInovciceNo($string){
        $year  = date('y');
        $month = date('m');
        
        if(date('d') == '01'){//on the month start date invoice will start from 01
            $day = '01';//if its a 1st day of month then assign 01 to day
        }else{//if its not 1st date then get the last record 
            $day   = Invoice::where('invoice_id', 'LIKE', '%'.$string.'%')->latest()->first();
            if ($day) {
                $parts = explode('-', $day->invoice_id);
                $day = isset($parts[2]) ? $parts[2] : null;
            } else {//if invoices table is empty and its not a first date then assign 01 to day
                $day = '01';
            }
        }
        $invoice = $string.'-'.$year.$month.'-'.$day;
        
        if(Invoice::where('invoice_id', $invoice)->doesntExist()){
            return $invoice;
        }else{
            return self::generateUniqueInvoiceNo($invoice);
        }
        
    }

    public static function generateUniqueInvoiceNo($invoice){
        $inv = Invoice::where('invoice_id', $invoice)->first();
        
        $firstDashPos = strpos($invoice, '-'); // Find the position of the first dash
        if ($firstDashPos !== false) {
            $secondDashPos = strpos(substr($invoice, $firstDashPos + 1), '-'); // Find the position of the second dash
            if ($secondDashPos !== false) {
                $secondDashPos += $firstDashPos + 1; // Adjust the position based on the substring
                $day            = substr($invoice, $secondDashPos+1);
                $newInvoiceId = str_replace($day, CommonHelpers::incrementNumber($day), $invoice);
                if(Invoice::where('invoice_id', $newInvoiceId)->exists()){
                    return self::generateUniqueInvoiceNo($newInvoiceId);
                }
                // dd($newInvoiceId);
                return $newInvoiceId;
            }
        }
    }

    public static function incrementNumber($value){
        // Convert the value to an integer
        $intValue = intval($value);

        // Check if the value is within the special range
        if ($intValue >= 1 && $intValue <= 9) {
            // Increment within the special range
            $intValue++;
            
            // Format the result back into a padded string
            $result = str_pad($intValue, 2, "0", STR_PAD_LEFT);
        } else {
            // Increment normally
            $result = sprintf('%02d', $intValue + 1);
        }
        return $result;  // Output: 02
    }

    public static function sendSms($mobile_no, $message, $sms_type=null){
        // return 'Success';
        // return 'Success';

        $setting = Cache::get('edit_setting');
        $sms     = Cache::get('sms_cache')->where('type', $sms_type)->first();
     
        if(is_null($sms)){
            $sms_status = 0;
        }else{
            $sms_status = $sms->status;
        }
        // dd($setting->is_sms);
        dd($sms_type);
        if($setting->is_sms == 1 && ($sms_status == 1 || $sms_type == 'manual')){
            $params = [
                'id'    => config('sms.sms_api_id'),
                'pass'  => config('sms.sms_api_pass'),
                'msg'   => $message,
                'to'    => $mobile_no,
                'lang'  => 'English',
                'mask'  => 'Gemnet',
                'type'  => 'json'
            ];
            $url  = config('sms.sms_api_url');
            $url  = $url.'?'.http_build_query($params);
            $response = Http::post($url);
            // dd($message);
            // $ch = curl_init($url);
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $response = curl_exec($ch); //This is the result from Outreach
            // curl_close($ch);
            $res = json_decode($response)->corpsms[0]->type;
            // dd(json_decode($response));
            // dd($mobile_no);
            if($res == 'Success'){
                return 'Success';
            }else{
                return false;
            }
        }
        return null;
    }

    public static function smsLog($user_id=null, $sms_type=null, $mobile_no=null, $sms, $status, $is_manual){
        SmsLog::insert([
            'user_id'   => @hashids_decode($user_id),
            'sms_type'  => $sms_type,
            'mobile_no' => $mobile_no,
            'sms'       => $sms,
            'status'    => $status,
            'is_manual' => $is_manual,
            'created_at'=>now(),
            'updated_at'=>now(),
        ]);
    }

    public static function sendSmsAndSaveLog($user_id=null, $username, $sms_type=null, $mobile_no=null, $amount=null, $package=null, $payment_type=null, $date=null){

        $sms = Sms::where('type',$sms_type)->first();

        if(strpos($sms->message, '$username') !== false){//check if $username exists in string
            $sms->message = str_replace('$username', $username, $sms->message);//replace the $username with the actual username
        }
        
        if(strpos($sms->message, '$amount') !== false && $amount != null){//check if $amount exists in string
            $sms->message = str_replace('$amount', $amount, $sms->message);//replace the $amount with the actual amount
        }

        if(strpos($sms->message, '$package') !== false && $package != null){//check if $package exists in string
            $sms->message = str_replace('$package', $package, $sms->message);//replace the $amount with the actual amount
        }

        if(strpos($sms->message, '$type') !== false && $payment_type != null){//check if $type exists in string
            $sms->message = str_replace('$type', $payment_type, $sms->message);//replace the $type with the actual payment_type
        }

        if(strpos($sms->message, '$date') !== false && $date != null){//check if $date exists in string
            $sms->message = str_replace('$date', $date, $sms->message);//replace the $date with the actual payment_type
        }

        $sms_status = self::sendSms($mobile_no, $sms->message, $sms_type);

        if($sms_status == 'Success'){//send sms and check status
            self::smsLog(hashids_encode($user_id), $sms_type, $mobile_no, $sms->message, 1,0);//save the success log
            return true;
        }elseif($sms_status != null){
            self::smsLog(hashids_encode($user_id), $sms_type, $mobile_no, $sms->message ?? '', 0,0);//save the failded log
            return false;
        }
        return null;

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
