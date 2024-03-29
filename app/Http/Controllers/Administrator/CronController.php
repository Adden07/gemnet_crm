<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\LogProcess;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PkgQueue;
use App\Models\QtOver;
use App\Models\QtReset;
use Illuminate\Http\Request;
use App\Models\RadCheck;
use App\Models\RadUserGroup;
use App\Models\User;
use App\Models\UserPackageRecord;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{   
    private $user_id = null;

    // public function __construct(Request $req)
    // {   
    //     dd('done');
    //     // if($req->ip() != '127.0.0.1'){
    //     //     abort(404);
    //     // }
    // }

    public function userExpiry(){
        $now    = date('Y-m-d');
        
        $now    = (date('H') == '00') ? $now.' 00:00' : $now.' 12:00';

        $users  = RadCheck::where('attribute','Expiration')
                       ->whereNotNUll('value')
                       ->whereRaw("STR_TO_DATE(`value`, '%d %M %Y %H:%i') <= '$now'")
                       ->get();//get the expired user's usernames
        $usernames     = $users->pluck('username')->toArray();//convert to array
        // dd(User::whereIn('username',$usernames)->where('status','!=','expired')->where('status', '!=', 'terminated ')->get()->pluck('username'));
        $count         = User::whereIn('username',$usernames)->where('status','!=','expired')->where('status', '!=', 'terminated')->get(['id']);//expire user status
        $counter = 0;
        // dd($count);
        // $updated_users = User::whereIn('username', $usernames)->where('status', 'expired')->get(['id', 'username', 'mobile']);
        foreach($count As $user){
            $user = User::find($user->id);
            $user->status = 'expired';
            $user->save();
            $this->logProcess($user->id, 3, null, 1);
            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_expired', $user->mobile);
            ++$counter;
        }
        // foreach($updated_users AS $user){
        //     $this->logProcess($user->id, 3, null, 1);
        //     CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_expired', $user->mobile);
        // }
        return "$counter Users Expired Successfully";
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
                    ->whereRaw('qt_used > qt_total')
                    ->where('qt_enabled', 1)
                    ->where('qt_expired', 0)
                    ->where('status', 'active')
                    ->get();//get those users whose qt_used are greater than qt_total
        
        $qt_over            = array();
        $kicked_users_count = 0;
  
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
                CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'qouta_over', $user->mobile, null,null );

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

    public function qtLow(){
        $users = User::whereRaw('(qt_used * 100) / qt_total > 90 AND (qt_used * 100) / qt_total < 100')->where('status', 'active')->get();
        $count = 0;
        foreach($users AS $user){
            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'low_qouta', $user->mobile, null,null );
            ++$count;
        }
        dd("$count users qouta is less then the 10%");
    }

    public function queue(){
        $current_expiraiton_users = User::whereDate('current_expiration_date', now())->get();
        $queue_users              = PkgQueue::with(['package'])->whereNUll('applied_on')
                                        ->whereIn('user_id', $current_expiraiton_users->pluck('id')->toArray())
                                        ->get();
        $rec = array(
            'success'   => 0,
            'failed'    => 0,
            'total'     => $current_expiraiton_users->count(),
        );
        foreach($queue_users AS $queue){
            try{
                DB::transaction(function() use (&$queue, &$rec){
                    $user                = User::findOrFail($queue->user_id);//get the user
                    $new_expiration_date = now()->parse($user->current_expiration_date)->addMonth($queue->package->duration)->format('Y-m-d 12:00');//create the expiraiton date
               
                    $this->user_id       = $user->id;//set the value to private variable to later access in catch
                    
                    $user->status                   = 'active';
                    $user->renew_by                 = 1;
                    $user->renew_date               = date('Y-m-d H:i:s');
                    $user->last_expiration_date     = $user->current_expiration_date;
                    $user->last_package             = $user->c_package;
                    $user->qt_total                 = $queue->package->volume;
                    $user->qt_used                  = 0;
                    $user->qt_enabled               = $queue->package->qt_enabled;
                    $user->package                  = $queue->package->id;
                    $user->c_package                = $queue->package->id;
                    $user->current_expiration_date  = $new_expiration_date;
                    $user->qt_expired               = 0;
                    $user->save();//update the users columns
                    
                    //update rad_user_group table
                    $this->updateRadUserGroup($user->username, $queue->package->groupname);
                    //update radcheck table
                    $this->updateRadCheck($user->username, $new_expiration_date);
                    //update user package record
                    $this->updateUserPackageRecord($user, $queue, $new_expiration_date);
                  
                    //update log process table
                    $this->logProcess($user->id, 1, $queue->id, 1);
                    //update queue table applied on column
                    $this->updatePkgQueue($queue->id);

                    $rec['success']   += 1;
                });
            }catch(Exception $e){
                $this->logProcess($this->user_id, 1, $queue->id, 0);
                $rec['failed']   += 1;
            }
        }
        return $rec;
    }

    public static function autoRenew($user_id=null){//auto renew users if user_id is give then only auto renew the specified user
        
        $auto_renew_users = User::when($user_id != null, function($query) use ($user_id){
                                $query->where('id', $user_id)->where('status', 'expired');
                            }, function($query){
                                $query->whereDate('current_expiration_date', now())->where('autorenew', 1);
                            })->where('autorenew',1)->orderBy('id', 'desc')->get();
        
        $rec = array(
            'success'   => 0,
            'failed'    => 0,
            'total'     => $auto_renew_users->count(),
            'failed_of_balance' => 0
        );
        
        foreach($auto_renew_users->chunk(10) AS $users){
            foreach($users AS $user){
                try{
                    DB::transaction(function() use (&$user, &$rec){
                        $package                = Package::findOrFail($user->package);
                        $site_setting           = Cache::get('edit_setting');
                        
                        //calculate the tax value
                        $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0)   ? ($package->price * $site_setting->mrc_sales_tax)/100: 0;
                        $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0) ? (($package->price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
                        $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;
                        //create the expiraiton date
                        $new_expiration_date = now()->parse($user->current_expiration_date)->addMonth($package->duration)->format('Y-m-d 12:00');//
                        //user balance calculation
                        $user_current_balance   = $user->user_current_balance;
                        $user_new_balance       = $user_current_balance-($package->price+$mrc_sales_tax+$mrc_adv_inc_tax);
                        $current_exp_date       = $user->current_expiration_date;
                       
                        if($user->user_current_balance < (intval($package->price+$mrc_total)) && $user->credit_limit == 0){
                            $rec['failed_of_balance'] += 1;
                            return;
                        }elseif(($user->credit_limit > (intval($package->price+$mrc_total))) || $user->credit_limit < (intval($package->price+$mrc_total))){
                            if((abs($user->credit_limit-abs($user->user_current_balance))) < (intval($package->price+$mrc_total))){
                                $rec['failed_of_balance'] += 1;
                                return;
                            }
                        }
                            $user->renew_by                 = 1;
                            $user->renew_date               = date('Y-m-d H:i:s');
                            $user->last_expiration_date     = $user->current_expiration_date;
                            $user->last_package             = $user->c_package;
                            $user->status                   = 'active';
                            $user->qt_total                 = $package->volume;
                            $user->qt_used                  = 0;
                            $user->qt_enabled               = $package->qt_enabled;
                            $user->package                  = $package->id;
                            $user->c_package                = $package->id;
                            $user->current_expiration_date  = $new_expiration_date;
                            $user->qt_expired               = 0;
                            $user->user_current_balance     = $user_new_balance;
                            $user->save();//update the users columns
                            
                            $transaction_id = rand(1111111111,9999999999);
                            $instance = new self();
                            //generate transction
                            $instance->generateTransaction($transaction_id, $user->id, $package->price, $mrc_total, $user_current_balance);
                            //generate invoice for this package
                            $instance->generateInvoice($transaction_id, $user->id, $package->id, $package->price,$current_exp_date, $new_expiration_date, $mrc_sales_tax, $mrc_adv_inc_tax, $mrc_total);
                            //update rad_user_group table
                            $instance->updateRadUserGroup($user->username, $package->groupname);
                            //update radcheck table
                            $instance->updateRadCheck($user->username, $new_expiration_date);
                            //update user package record
                            $instance->updateUserPackageRecord($user, null, $new_expiration_date);
                            
                            //update log process table
                            $instance->logProcess($user->id, 2, null, 1);
                            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_renew', $user->mobile, null, $package->name);
                            @CommonHelpers::kick_user_from_router(hashids_encode($user->id));
                            $rec['success'] += 1;
                    });
                }catch(Exception $e){
                    $rec['failed'] += 1;
                }
            }
        }
        return $rec;
    }

    public function updateRadUserGroup($username, $groupname){//update rad user group
        $rad_user_group = RadUserGroup::where('username',$username)->firstOrFail();
        $rad_user_group->groupname = $groupname;
        $rad_user_group->save();
    }

    public function updateRadCheck($username, $date){//ipdate rad check table
        $rad_check = RadCheck::where('username',$username)->where('attribute','Expiration')->firstOrFail();
        $rad_check->value = date('d M Y 12:00',strtotime($date));;
        $rad_check->save();  
    }

    public function updateUserPackageRecord($user, $queue, $date){//update user package record table
        $user_package_record                 = new UserPackageRecord();
        $user_package_record->admin_id       = 1;
        $user_package_record->user_id        = $user->id;  
        $user_package_record->package_id     = $queue->package->id ?? $user->c_package;
        $user_package_record->last_package_id= $user->c_package;
        $user_package_record->status         = 'renew';
        $user_package_record->last_expiration = $user->current_expiration_date;
        $user_package_record->expiration     = $date;
        $user_package_record->created_at     = date('y-m-d H:i:s');
        $user_package_record->save();
    }

    public function logProcess($user_id, $process_id, $queue_id, $status){//insert logs ig log process table
        $arr = array(
            'user_id'   => $user_id,
            'process'   => $process_id,
            'queue_id'  => $queue_id,
            'status'    => $status,
            'created_at'=>now(),
            'updated_at'=> now(),
        );
        LogProcess::insert($arr);
    }

    public function updatePkgQueue($queue_id){//update pkg queue column
        $queue = PkgQueue::findOrFail($queue_id);
        $queue->applied_on = now();
        $queue->save();
    }

    public function generateTransaction($transaction_id, $user_id, $package_price, $mrc_total, $user_current_balance){
        $transaction_arr = array(// array for transaction table
            'transaction_id'    => $transaction_id,
            'admin_id'          => 1,
            'user_id'           => $user_id,
            'amount'            => ($package_price+$mrc_total),
            'old_balance'       => $user_current_balance,
            'new_balance'       => $user_current_balance-($package_price+$mrc_total),
            'type'              => 0,
            'created_at'        => date('Y-m-d H:i:s')
        );
        Ledger::insert($transaction_arr);
    }

    public function generateInvoice($transaction_id, $user_id, $package_id, $package_price, $current_exp_date, $new_exp_date, $mrc_sales_tax, $mrc_adv_inc_tax, $mrc_total){
        $invoice                    = new Invoice();
        $invoice->invoice_id        = CommonHelpers::generateInovciceNo('GP');
        $invoice->transaction_id    = $transaction_id;
        $invoice->admin_id          = 1;
        $invoice->user_id           = $user_id;
        $invoice->pkg_id            = $package_id;
        $invoice->pkg_price         = $package_price;
        $invoice->type              = 1;
        $invoice->current_exp_date  = $current_exp_date;
        $invoice->new_exp_date      = $new_exp_date;
        $invoice->created_at        = date('Y-m-d H:i:s');
        $invoice->sales_tax         = $mrc_sales_tax;
        $invoice->adv_inc_tax       = $mrc_adv_inc_tax;
        $invoice->total             = round($package_price+$mrc_total);
        $invoice->save();
    }

    public function expiry(){
        $arr = array();
        try{
            DB::transaction(function() use (&$arr){
                $arr['queue']       = $this->queue();
                $arr['auto_renew']  = $this->autoRenew();
                $arr['expiry']      = $this->userExpiry();
            });
        }catch(Exception $e){
            dd('Some Erro occoured');
        }
        dd($arr);
    }

    public function updateTransactionImagePath(){
        set_time_limit(0);
        $payment = Payment::whereNotNUll('transaction_image')->get(['id', 'transaction_image']);
        $total   = 0;
        foreach($payment AS $path){
            $payment = Payment::findOrFail($path->id);
            $new_path = 'admin_uploads/transactions/2023/'.basename(strchr($path->transaction_image, '/'));
            $payment->transaction_image = $new_path;
            $payment->save();
            $total += 1;
        }
        dd("Total updated row $total");
    }

    public function usersAboutToExpire(){

        $users = User::whereBetween('current_expiration_date', [now(), now()->addDays(3)])->get(['id', 'username', 'mobile', 'current_expiration_date', 'c_package', 'user_current_balance']);
        $site_setting           = Cache::get('edit_setting');
        $count                  = 0;
        $arr                    = [];
        foreach($users->chunk(100) AS $chunk){
            foreach($chunk AS $user){
                $package                = Package::findOrFail($user->c_package);
                //calculate the tax value
                $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0)   ? ($package->price * $site_setting->mrc_sales_tax)/100: 0;
                $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0) ? (($package->price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
                $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;
                
                
                if(intval($user->user_current_balance+abs($user->credit_limit)) < (intval($package->price+$mrc_total))){//if user balance is greater then the pkg_price+mrc
                    if(CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_near_expiry', $user->mobile,null,null,null,$user->current_expiration_date) != null){
                        ++$count;
                    }
                }
            }
        }
        dd("Send sms to $count users");
    }

    public function resetQoutaUnapaidUsers(){
        set_time_limit(0);
        $users = User::with(['primary_package'])->where('paid', 0)->where('qt_enabled',1)->get();//find user
        // dd($users);
        $counter = 0;
        foreach($users AS $user){
            $user_qt_expired = $user->qt_expired;
            if($user->qt_expired == 1){
                $user->qt_expired = 0;
                $user->c_package  = $user->package;//replace current package with primay package
                //update raduesrgroup table because we have updated the package
                RadUserGroup::where('username',$user->username)->update(['groupname'=>$user->primary_package->groupname]);
            }
            $user->qt_used = 0;
            $user->save();
            QtReset::insert([
                'user_id'       => $user->id,
                'package_id'    => $user->package,
                'type'          => 'crone'
            ]);
            if(is_null($user->last_login_time) || $user_qt_expired == 1){//kick user if user if online or qt_expired is 1
                CommonHelpers::kick_user_from_router($user->hashid);
            }
            ++$counter;
        }
        dd("$counter users qouta reset");
    }
}
