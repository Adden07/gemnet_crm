<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Package;
use App\Models\RadUserGroup;
use App\Models\RadCheck;
use App\Models\UserPackageRecord;
use App\Models\Admin;
use App\Models\FranchisePackage;
use App\Models\Invoice;
use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Helpers\CommonHelpers;
use App\Models\PkgQueue;
use Exception;
use Illuminate\Support\Facades\Cache;

class PackageController extends Controller
{
    //add user package
    public function addUserPackage($id){
        if(isset($id) && !empty($id)){
            $data = array(
                'user'             => User::with(['rad_check','packages'])->findOrFail(hashids_decode($id)),
                'user_package_id'  => UserPackageRecord::where('user_id',hashids_decode($id))->latest()->first(),
                'packages'         => Package::whereBetween('usertype',[1,10] )->get(),
            );
         
            $html = view('admin.user.package_modal')->with($data)->render();
            return response()->json([
                'html'  => $html
            ]);
        }
    }

    //active and renew user package
    public function updateUserPackage(Request $req){

        $rules = [
            'username'   => ['required', 'max:191'],
            'status'     => ['required', 'in:registered,active,expired'],
            'package_id' => [Rule::requiredIf($req->renew_type == 'queue')],
            'user_id'    => ['required'],
            'month_type' => [Rule::requiredIf(empty($req->renew_type)),'in:monthly,half_month,full_year,promo'],
            // 'calendar'   => [Rule::requiredIf($req->month_type != 'monthly')],
            'otc'        => ['nullable', 'in:1,0'], 
            'renew_type' => [Rule::requiredIf(empty(!$req->renew_type)), 'in:immediate,queue'] 
        ];

        $validator = Validator::make($req->all(),$rules);
        
        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $validated  = $validator->validated();
        $msg        = '';
        $user       = User::findOrFail(hashids_decode($validated['user_id']));

        if(isset($validated['renew_type']) && $validated['renew_type'] == 'immediate'){
            $validated['package_id'] =$validated['package_id'];
        }
        // dd(hashids_decode($validated['package_id']));
        $package    = Package::findOrFail(hashids_decode($validated['package_id']));
        
        $package->id = (!is_null($package->m_pkg)) ? $package->m_pkg : $package->id;
        
        $site_setting           = Cache::get('edit_setting');
        //calculate the tax value
        $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0 && $user->is_tax == 1)   ? ($package->price * $site_setting->mrc_sales_tax)/100: 0;
        $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0 && $user->is_tax == 1) ? (($package->price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
        $otc_sales_tax          = ($site_setting->mrc_adv_inc_tax != 0 && $req->otc == 1 && $user->is_tax == 1) ? ($package->otc * $site_setting->otc_sales_tax)/100: 0;
        $otc_adv_inc_tax        = ($site_setting->otc_adv_inc_tax != 0 && $req->otc == 1 && $user->is_tax == 1) ? (($package->otc+$otc_sales_tax) * $site_setting->otc_adv_inc_tax)/100: 0;
        $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;
        $otc_total              = $otc_sales_tax+$otc_adv_inc_tax;

        // if(isset($validated['calendar']) && !empty($validated['calendar'])){
        //     $date    = $validated['calendar'];
        // }else{
        //     // $date    = Carbon::now()->addMonth()->format('d-M-Y 12:00');//get date of 1 month from today date 
        //     $date = now()->addMonth($package->duration)->format('d-M-Y 12:00');
        // }
        $date = now()->parse($user->current_expiration_date)->addMonth($package->duration)->format('d-M-Y 12:00');
        //when renew the package then check package exists or not
        // if(auth()->user()->user_type != 'admin'){//if user is not admin
        //     if($validated['status'] == 'active'){//if its renew
        //         if(FranchisePackage::where('added_to_id',auth()->user()->id)->where('package_id',hashids_decode($validated['package_id']))->where('status','active')->doesntExist()){//check if pacakge does not exists and active
        //             return response()->json([
        //                 'error' => 'Could not renew the package please change the package',
        //             ]);
        //         }
        //     }
        // }
        if($user->paid == 1){
            if($user->user_current_balance < (intval($package->price+$mrc_total))){
                $err =  [
                    'error' => 'User balance is less than the package price'
                ];

                if(@$validated['otc'] == 1 && $user->user_current_balance < ($package->price+$package->otc+$mrc_total) && $user->credit_limit == 0){
                    return [
                        'error' => 'User balance is less than the package price and OTC price'
                    ];    
                }

                if($user->credit_limit > (intval($package->price+$mrc_total))){//
                    if(($user->credit_limit-abs($user->user_current_balance)) < (intval($package->price+$mrc_total))){
                        return [
                            'error' => 'User credit limit is less than the package price'
                        ];
                    }
                }elseif($user->credit_limit+$user->user_current_balance < (intval($package->price+$mrc_total))){
                    return [
                        'error' => 'User credit limit is less than the package price'
                    ];
                }
            }
        }

        // dd('done');
        // if($validated['status'] == 'registered'){//if user is register and its current balance is less than package price + otc through errors
        //     if($user->user_current_balance < ($package->price+$package->otc)){
        //         return [
        //             'error' => 'User balance is less than the package price and OTC price'
        //         ];
        //     }
        // }
        // //if user curen balance is less than the package price than throw error
        // if($user->user_current_balance < $package->price){
        //     return [
        //         'error' => 'User balance is less than the package price'
        //     ];
        // }
        
        DB::transaction(function() use ($validated,$date, &$user, &$package, &$mrc_sales_tax, &$mrc_adv_inc_tax, &$otc_sales_tax, &$otc_adv_inc_tax, &$mrc_total, &$otc_total, &$site_setting){
            
            $user_status            = $user->status;
            $last_expiration_date   = $user->current_expiration_date;
            $last_package           = $user->package;
            $user_current_balance   = $user->user_current_balance;
            
            if($user->paid == 1){
                //calculat user new balance
                $user_new_balance       = $user_current_balance-($package->price+$otc_total+$mrc_total);
                $user_new_balance       -= (@$validated['otc'] == 1) ? $package->otc : 0;
            }else{
                $user_new_balance       = $user_current_balance;
            }

            //when renew the package add the one month in last expiration date            
            if($validated['status'] == 'active' || $validated['status'] == 'expired'){
                $activity_log = "renewed user - ($user->username)";
                // global $msg          = 'Package Renewed Successfully';
                $GLOBALS['msg'] = 'Package Renewed Successfully';
                
                //if status is expired then calculate the new date from today's date
                //if($validated['status'] == 'expired'){
                    //if month type is month and status is expired then get the current date otherwise get the calendar date for expiry
                    // if($validated['month_type'] == 'monthly' && empty($validated['calendar'])){
                    //     $date = date('Y-m-d 12:00');
                    // }else{
                    //     $date = date('Y-m-d 12:00',strtotime($validated['calendar']));
                    // }
                //}else{//if status is not expired then calculate the date from the db date
                    //if month type is month and status is expired then get the current date otherwise get the calendat date for expiry
                    // if($validated['month_type'] == 'monthly' && empty($validated['calendar'])){
                    //     $date = $user->current_expiration_date;
                    // }else{
                    //     $date = date('Y-m-d 12:00',strtotime($validated['calendar']));
                    // }
                    //$date = $user->current_expiration_date;
                //}

                //create expiry date 
                // if($validated['month_type'] == 'monthly' && empty($validated['calendar'])){//expir
                //     $current_exp_date =  date('Y-m-d H:i:s',strtotime($date));
                //     $date             = Carbon::parse($date)->addMonth()->format('d-M-Y 12.00');
                //     $new_exp_date     = date('Y-m-d H:i:s',strtotime($date));//converting back to DB datetime  format
                // }else{//expiry date from calendar date
                //     $current_exp_date =  date('Y-m-d H:i:s',strtotime($date));
                //     $date             = Carbon::parse($date)->format('d-M-Y 12.00');
                //     $new_exp_date     = date('Y-m-d H:i:s',strtotime($date));//converting back to DB datetime  format
                // }
                $current_exp_date =  date('Y-m-d H:i:s',strtotime($date));
                // $date             = Carbon::parse($date)->addMonth()->format('d-M-Y 12.00');
                $new_exp_date     = date('Y-m-d H:i:s',strtotime($date));//converting back to DB datetime  format
                    // dd($new_exp_date);

                $user->renew_by             = auth()->user()->id;
                $user->renew_date           = date('Y-m-d H:i:s');
                $user->last_expiration_date = $last_expiration_date;
                $user->last_package         = $last_package;
                $package_status             =1;
                
                if(isset($validated['renew_type']) && @$validated['renew_type'] == 'queue'){
                    $user->user_current_balance     = $user_new_balance;
                }else{
                    $user->status                   = 'active';
                    $user->qt_total                 = $package->volume;
                    $user->qt_used                  = 0;
                    $user->qt_enabled               = $package->qt_enabled;
                    $user->package                  = $package->id;
                    $user->c_package                = $package->id;
                    $user->current_expiration_date  = $new_exp_date;
                    $user->qt_expired               = 0;
                    $user->user_current_balance     = $user_new_balance;
                }
            }else{//means user is registered
                $package_status             =0;
                $current_exp_date = null;
                $new_exp_date     = date('Y-m-d H:i:s',strtotime($date));
                $user->activation_by = auth()->user()->id;
                $user->activation_date = date('Y-m-d H:i:s');
                $user->user_current_balance     = $user_new_balance;
                $user->status                   = 'active';
                $user->qt_total                 = $package->volume;
                $user->qt_used                  = 0;
                $user->qt_enabled               = $package->qt_enabled;
                $user->package                  = $package->id;
                $user->c_package                = $package->id;
                $user->current_expiration_date  = $new_exp_date;
                $user->qt_expired               = 0;
                $activity_log   = "activated user-($user->username)";
                $GLOBALS['msg'] = 'Package Activated Successfully';
            }
            //update user and set status active even status is registered or expired

            $user->macs                     = auth()->user()->user_mac;
            // $user->last_expiration_date     = $current_exp_date;
            $user->save();
            //if its renew then find if its registered then create new record
            $rad_user_group = RadUserGroup::where('username',$user->username)->firstOrNew();

            if(($validated['status'] == 'active' || $validated['status'] == 'expired') && isset($validated['renew_type']) && $validated['renew_type'] != 'queue'){
                $rad_user_group->groupname = $package->groupname;
            }else{
                $rad_user_group->username   = $user->username;
                $rad_user_group->groupname = $package->groupname;
                $rad_user_group->priority   = 1;
            }
            
            $rad_user_group->save();
            //if user status is registered then insert 2 rows in rad_checks otherwise update expiration value
            if($validated['status'] == 'registered'){
                
                $rad_check              = new RadCheck;
                $rad_check->username    = $user->username;
                $rad_check->attribute   = 'Cleartext-Password';
                $rad_check->op          = ':=';
                $rad_check->value       = $user->password;
                $rad_check->save();
    
                $rad_check               = new RadCheck;
                $rad_check->username     = $user->username;
                $rad_check->attribute    = 'Expiration';
                $rad_check->op           = ':=';
                $rad_check->value        = date('d M Y 12:00',strtotime($date));
                $rad_check->save();
            }elseif(isset($validated['renew_type']) && $validated['renew_type'] != 'queue'){
                $rad_check = RadCheck::where('username',$user->username)->where('attribute','Expiration')->first();
                $rad_check->value = date('d M Y 12:00',strtotime($date));;
                $rad_check->save();           
            }
            
            //insert data in user_package_record to record the user renew and activate package activity
            if($validated['status'] == 'active' || $validated['status'] == 'expired'){
                $status          = 'renew';
                $in_status       = 'renew'; //status for invoice
            }else{
                $status          = 'activate';
                $in_status       = 'new';
            }

            if($user->paid == 1){
                if(isset($validated['renew_type']) && $validated['renew_type'] != 'queue'){
                    $user_package_record                 = new UserPackageRecord;
                    $user_package_record->admin_id       = auth()->user()->id;
                    $user_package_record->user_id        = $user->id;  
                    $user_package_record->package_id     = $package->id;
                    $user_package_record->last_package_id= $last_package;
                    $user_package_record->status         = $status;
                    $user_package_record->last_expiration = $last_expiration_date;
                    // $user_package_record->package_status = $package_status;
                    $user_package_record->expiration     = date('y-m-d H:i:s',strtotime($date));
                    $user_package_record->created_at     = date('y-m-d H:i:s');
                    $user_package_record->save();
                }
                
                $transaction_id  = rand(1111111111,9999999999);
                $transaction_arr = array(// array for transaction table
                    'transaction_id'    => $transaction_id,
                    'admin_id'          => auth()->id(),
                    'user_id'           => $user->id,
                    'amount'            => ($package->price+$mrc_total),
                    'old_balance'       => $user_current_balance,
                    'new_balance'       => $user_current_balance-($package->price+$mrc_total),
                    'type'              => 0,
                    'created_at'        => date('Y-m-d H:i:s')
                );
                
                $inv_id     = rand(1111111111,9999999999);
                Ledger::insert($transaction_arr);
                //insert data in invoices
                $invoice                    = new Invoice;
                $invoice->invoice_id        = CommonHelpers::generateInovciceNo('GP');
                $invoice->transaction_id    = $transaction_id;
                $invoice->admin_id          = auth()->id();
                $invoice->user_id           = $user->id;
                $invoice->pkg_id            = $package->id;
                $invoice->pkg_price         = $package->price;
                $invoice->type              = $package_status;
                $invoice->current_exp_date  = $current_exp_date;
                $invoice->new_exp_date      = $new_exp_date;
                $invoice->created_at        = date('Y-m-d H:i:s');
                $invoice->sales_tax         = $mrc_sales_tax;
                $invoice->adv_inc_tax       = $mrc_adv_inc_tax;
                $invoice->total             = round($package->price+$mrc_total);
                $invoice->taxed             = ($user->is_tax == 1) ? 1 : 0;
                $invoice->save();
            }

            if(isset($validated['renew_type']) && $validated['renew_type'] == 'queue'){
                $pkg_queue_arr = array(
                    'queue_by'  => 1,
                    'invoice_id'=> $inv_id ?? 0,
                    'user_id'   => $user->id,
                    'package_id' => $package->id,
                    'applied_on' => Null,
                    'created_at' => now(),
                    'updated_at' => now(),
                );
                PkgQueue::insert($pkg_queue_arr);
            }

            if(isset($validated['otc']) && $validated['otc'] == 1 && $validated['status'] == 'registered' && $user->is_tax == 1 && $user->paid == 1){//if user is register and otc is     true then creat another transaction and invoice
              
                $transaction_id = rand(1111111111,9999999999);
                $transaction_arr = array(// array for transaction table
                    'transaction_id'    => $transaction_id,
                    'admin_id'          => auth()->id(),
                    'user_id'           => $user->id,
                    'amount'            => ($package->otc+$otc_total),
                    'old_balance'       => $user_current_balance-($package->price+$mrc_total),
                    'new_balance'       => ($user_current_balance-($package->price+$mrc_total))-($package->otc+$otc_total),
                    'type'              => 0,
                    'created_at'        => date('Y-m-d H:i:s')
                );
                Ledger::insert($transaction_arr);
                //insert data in invoices
                $invoice                    = new Invoice;
                $invoice->invoice_id        = CommonHelpers::generateInovciceNo('OTC');
                $invoice->transaction_id    = $transaction_id;
                $invoice->admin_id          = auth()->id();
                $invoice->user_id           = $user->id;
                $invoice->pkg_id            = $package->id;
                $invoice->pkg_price         = $package->otc;
                $invoice->type              = (int) 2;
                $invoice->current_exp_date  = null;
                $invoice->new_exp_date      = null;
                $invoice->created_at        = date('Y-m-d H:i:s');
                $invoice->sales_tax         = $otc_sales_tax;
                $invoice->adv_inc_tax       = $otc_adv_inc_tax;
                $invoice->total             = round($package->otc+$otc_total);
                $invoice->taxed             = ($user->is_tax == 1) ? 1 : 0;
                $invoice->save();
            }
         
            //if user status is expired and user is online then kick user
            if($validated['status'] == 'expired' && $user->last_logout_time == null){
                CommonHelpers::kick_user_from_router($validated['user_id']);//kick user
            }
            ($validated['status'] == 'registered') ? 
            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_activation', $user->mobile, null, $package->name) :             
            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_renew', $user->mobile, null, $package->name);


            CommonHelpers::activity_logs($activity_log);
        });


        return response()->json([
            'success'   => $GLOBALS['msg'],
            'reload'    => TRUE
        ]);
        
    }

    //user_id will be the id of franchise,dealer,subdealer id and user_type will be the type of franchise,dealer,subdealer
    private function getPackageCost($user_id,$user_type,$pkg_id){
        $arr = array();
        if($user_type == 'franchise'){
        }elseif($user_type == 'dealer'){

        }elseif($user_type == 'sub_dealer'){
            $arr['sub_dealer']     = $this->updateUserBalance($user_id,$pkg_id);
            $arr['dealer']         = $this->updateUserBalance($arr['sub_dealer']['edit_by_id'],$pkg_id);
            $arr['franchise']      = $this->updateUserBalance($arr['dealer']['edit_by_id'],$pkg_id);
            // dd($arr);
        }
    }

    private function updateUserBalance($user_id,$pkg_id){

        $user_package = FranchisePackage::where('added_to_id',$user_id)->where('package_id',1)->where('status','active')->firstOrFail();
        $user         = Admin::where('id',$user_id)->firstOrFail();
        
        $old_balance = $user->balance;
        $new_balance = $user->balance - $user_package->cost;

        $user->decrement('balance',$user_package->cost);
        $user->save();

        return array('user_id'=>$user_id,'edit_by_id'=>$user_package->edit_by_id,'old_balance'=>$old_balance,'new_balance'=>$new_balance,'cost'=>$user_package->cost);
    }
    //change user pacakge
    public function changeUserPackage(Request $req){
        
        if(CommonHelpers::rights('enabled-user','change-user-package')){
            return redirect()->route('admin.home');
        }
        
        $rules = [
            'user_id'       => ['required'],
            'package_id'    => ['required'],
            'package_type'  => ['nullable','in:primary,current']
        ];
        
        $messages = [
            'required'          => 'Same package selected',
            'user_id.required'  => 'User not found'
        ];

        $validator = Validator::make($req->all(), $rules, $messages);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        
        $validated = $validator->validated();

        $user  = User::findOrFail(hashids_decode($validated['user_id']));//get user
        //check package change time
        $t1 = strtotime(date('Y-m-d H:i:s'));
        $t2 = strtotime($user->last_pkg_chg_time);
        $diff              = $t1 - $t2;
        $hours             = $diff / (60 * 60);
        
        // if($hours <= 24 && $user->last_pkg_chg_time != NULL && auth()->user()->user_type != 'admin'){
        //     return response()->json([
        //         'error' => 'You Cannot Change Package Again in Last 24 Hours'
        //     ]);
        // }

        DB::transaction(function() use ($validated){
            
            $user                = User::findOrFail(hashids_decode($validated['user_id']));//get user
            $package             = Package::findOrFail(hashids_decode($validated['package_id']));//get package
            $user_package_record = new UserPackageRecord;//new entry
            $rad_user_group      = RadUserGroup::where('username',$user->username)->firstOrFail();
            $last_package_record = UserPackageRecord::where('user_id',hashids_decode($validated['user_id']))->orderBy('id','DESC')->first();



            $user_qt_expired     = $user->qt_expired;
            $activity_log        = "change packaged-($user->username)";
            //insert record in user_package_record
            $user_package_record->admin_id       = auth()->user()->id;
            $user_package_record->user_id        = $user->id;
            $user_package_record->package_id     = $package->id;
            $user_package_record->status         = 'change';
            // $user_package_record->package_status = $user->status;
            
            $user_package_record->expiration     =  $user->current_expiration_date; 
            $user_package_record->created_at     = date('y-m-d H:i:s');
            $user_package_record->last_package_id= @$user->last_package;
            $user_package_record->last_expiration= @$user->expiration->last_expiration_date;
            $user_package_record->save();
            //update radusergroup table
            $rad_user_group->groupname = $package->groupname;
            $rad_user_group->save();
            //update users table when pkg type primary update current and primary pkg otherwise update only current pkg
            
            if($validated['package_type'] == 'primary'){
                $user->package   = $package->id;
                $user->c_package = $package->id;
            }else{
                $user->c_package = $package->id;
                $user->package   = $package->id;

            }
            $user->last_pkg_chg_time = date('Y-m-d H:i:s');
            $user->save();
            
            if(is_null($user->last_logout_time) || $user_qt_expired == 1){//when user is online or qt_expired is 1 then kick user
                CommonHelpers::kick_user_from_router($validated['user_id']);//kick user
            }

            CommonHelpers::activity_logs($activity_log);
        });

        return response()->json([
            'success'   => 'Package Updated Successfully',
            'reload'    => TRUE
        ]);
    }

    //upgrade user package modal
    public function upgradeUserPackageModal($id){
        if(CommonHelpers::rights('enabled-user','upgrade-user-package')){
            return redirect()->route('admin.home');
        }
        
        
        if(isset($id) && !empty($id)){
            $data = array(
                'user'             => User::with(['current_package'])->findOrFail(hashids_decode($id)),
                // 'user_package_id'  => UserPackageRecord::where('user_id',hashids_decode($id))->latest()->first(),
            );
            
            // $user_packages  = FranchisePackage::where('added_to_id',auth()->user()->id)->get();

            // $cost = $user_packages->where('package_id',$data['user']->package)->pluck('cost')->first();
            
            // $packages               = Package::get();
            // $user_packages          = $user_packages->where('cost','>',$cost);//get user packages where cost less then or equal to cost
  
            // if(auth()->user()->user_type == 'franchise'){//if user is franchise 
            //     $ids = $user_packages->where('status','active')->pluck('package_id')->toArray();
            //     $data['packages'] = $packages->whereIn('id',$ids);
            // }elseif(auth()->user()->user_type == 'dealer'){//if user is dealer then get only those pacakges which are assigned and active in franchise
            //     $ids = DealerController::getParentActivePacakges($user_packages);
            //     $data['packages'] = $packages->whereIn('id',$ids);
            // }elseif(auth()->user()->user_type == 'sub_dealer'){//if user is subealer then get those packagese which are assigned  active in franchise and dealer
            //     $ids = SubDealerController::getParentActivePacakges($user_packages);
            //     $data['packages'] = $packages->whereIn('id',$ids);
            //     $data['user_packages'] = $user_packages;
            //     $data['ids']           = $ids;
            // }else{
            //     $data['packages'] = $packages;
            // }

            $data['packages'] = Package::get();
            $data['user_current_package'] = $data['packages']->where('id', $data['user']->package)->first();
            
            if($data['user_current_package']->duration ==1){
                $data['packages'] = $data['packages']->where('price', '>', $data['user_current_package']->price)->where('duration',1);
            }
            
            $html = view('admin.user.upgrade_package_modal')->with($data)->render();

            return response()->json([
                'html'  => $html
            ]);
        }
    }

    //change user package
    public function upgradeUserPackage(Request $req){
        
        if(CommonHelpers::rights('enabled-user','upgrade-user-package')){
            return redirect()->route('admin.home');
        }

        $rules = [
            'user_id'       => ['required'],
            'package_id'    => ['required'],
        ];

        $messages = [
            'package_id.required' => 'Please select the package',
            'user_id.required'    => 'User not found'
        ];
        
        $validator = Validator::make($req->all(), $rules, $messages);
        
        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $validated = $validator->validated();
        $msg       = [
            'success'   =>'Package upgraded successfully',
            'reload'    => true
        ];


        //calculate the tax value
        // $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0 && $user->is_tax == 1)   ? ($package->price * $site_setting->mrc_sales_tax)/100: 0;
        // $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0 && $user->is_tax == 1) ? (($package->price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
        // $otc_sales_tax          = ($site_setting->mrc_adv_inc_tax != 0 && $req->otc == 1 && $user->is_tax == 1) ? ($package->otc * $site_setting->otc_sales_tax)/100: 0;
        // $otc_adv_inc_tax        = ($site_setting->otc_adv_inc_tax != 0 && $req->otc == 1 && $user->is_tax == 1) ? (($package->otc+$otc_sales_tax) * $site_setting->otc_adv_inc_tax)/100: 0;
        // $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;
        // $otc_total              = $otc_sales_tax+$otc_adv_inc_tax;
        
        // if($user->user_current_balance < ($package->price+$package->otc+$mrc_total)){
        //     return [
        //         'error' => 'User balance is less than the package price and OTC price'
        //     ];
        // }

        DB::transaction(function() use ($validated, &$msg){
            
            $user                = User::findOrFail(hashids_decode($validated['user_id']));//get user
            $package             = Package::findOrFail(hashids_decode($validated['package_id']));//get package
            $site_setting        = Cache::get('edit_setting');
            $user_invoice        = Invoice::where('user_id', $user->id)->whereIn('type', [0,1])->latest()->first();



            
            $transaction_id  = rand(1111111111,9999999999);
            $current_date    = date_create(date('Y-m-d 12:00:00'));
            $exp_date        = date_create($user->current_expiration_date);
            $remaining_days  = date_diff($current_date,$exp_date)->format("%a");
            ($remaining_days == 31) ? $remaining_days = 30 : ''; //if remaining days are equal to 31 then make it 30 days

            $get_current_pkg_per_day_price = (int) $this->getPacakgePerDayPrice($user_invoice->total, $remaining_days);//get per day price of existig package
            $get_new_pkg_per_day_price     = (int) $this->getPacakgePerDayPrice($package->price, $remaining_days);//get per day price of new selected package
           
            $new_package_price_tax_arr     =  $this->getPackagePriceWithTax($get_new_pkg_per_day_price, $remaining_days);
            $get_new_pkg_price_with_tax    = (int) $new_package_price_tax_arr['package_price'];//get total price of new package with tax
            $get_current_pkg_price         = (int) $get_current_pkg_per_day_price*$remaining_days;//get curren
            $pkg_price_to_deduct           = (int) $get_new_pkg_price_with_tax-$get_current_pkg_price;
            $new_pkg_price_without_tax     = (int) $new_package_price_tax_arr['package_price']-$get_current_pkg_price;

            if($user->paid == 1){
                if($user->user_current_balance < $pkg_price_to_deduct && $user->credit_limit == 0){
                    throw new Exception("Package could be upgrade because of insufficent balance");
                }elseif(($user->credit_limit > ($pkg_price_to_deduct)) || $user->credit_limit < ($pkg_price_to_deduct)){
                    if(abs(($user->credit_limit-($user->user_current_balance))) < ($pkg_price_to_deduct)){
                        throw new Exception("User credit limit is less than the package price");
                    }
                }
            }

            $user_current_balance   = $user->user_current_balance;
            $user_new_balance       = ($user->paid == 1) ? $user_current_balance-$pkg_price_to_deduct : $user->user_current_balance;
            $user_current_pkg       = $user->c_package;
            $user_qt_expired        = $user->qt_expired;
            $activity_log            = "upgraded package-($user->username)";
            $user->last_package      = $user->package;
            $user->user_current_balance = $user_new_balance;
            $user->package           = $package->id;
            $user->c_package         = $package->id;
            $user->save();
            
            if($user->paid == 1){
                $transaction_id  = rand(1111111111,9999999999);
                $transaction_arr = array(// array for transaction table
                    'transaction_id'    => $transaction_id,
                    'admin_id'          => auth()->id(),
                    'user_id'           => $user->id,
                    'amount'            => $pkg_price_to_deduct,
                    'old_balance'       => $user_current_balance,
                    'new_balance'       => ($user_current_balance-$pkg_price_to_deduct),
                    'type'              => 3,
                    'created_at'        => date('Y-m-d H:i:s')
                );
                Ledger::insert($transaction_arr);
                //insert data in invoices
                $invoice                    = new Invoice;
                $invoice->invoice_id        = CommonHelpers::generateInovciceNo('GP');
                $invoice->transaction_id    = $transaction_id;
                $invoice->admin_id          = auth()->id();
                $invoice->user_id           = $user->id;
                $invoice->pkg_id            = $package->id;
                $invoice->pkg_price         = $new_pkg_price_without_tax;
                $invoice->type              = 3;
                $invoice->current_exp_date  = $user_invoice->current_exp_date;
                $invoice->new_exp_date      = $user_invoice->new_exp_date;
                $invoice->created_at        = date('Y-m-d H:i:s');
                $invoice->sales_tax         = $new_package_price_tax_arr['mrc_sales_tax'];
                $invoice->adv_inc_tax       = $new_package_price_tax_arr['mrc_adv_inc_tax'];
                $invoice->total             = $pkg_price_to_deduct;
                $invoice->taxed             = ($user->is_tax == 1) ? 1 : 0;
                $invoice->save();
    
    
                $user_package_record = new UserPackageRecord;//new entry
                //insert record in user_package_record
                $user_package_record->admin_id       = auth()->user()->id;
                $user_package_record->user_id        = $user->id;
                $user_package_record->package_id     = $package->id;
                $user_package_record->status         = 'upgrade';
                $user_package_record->expiration     =  $user->current_expiration_date; 
                $user_package_record->created_at     = date('y-m-d H:i:s');
                $user_package_record->last_package_id= $user_current_pkg;
                $user_package_record->last_expiration= $user->current_expiration_date;
                $user_package_record->save();
            }
            
            $rad_user_group = RadUserGroup::where('username',$user->username)->first();
            $rad_user_group->groupname = $package->groupname;
            $rad_user_group->save();

            if(is_null($user->last_logout_time || $user_qt_expired == 1)){//when user is online or qt_expired is 1 then kick user
                CommonHelpers::kick_user_from_router($validated['user_id']);//kick user
            }
            CommonHelpers::activity_logs($activity_log);
        });
        return response()->json($msg);
    }

    public function getPacakgePerDayPrice($package_price, $days){
        return (int) round(($package_price/30));
    }

    public function getPackagePriceWithTax($package_price, $days){
        $package_price       = $package_price*$days;
        $site_setting        = Cache::get('edit_setting');

        //calculate the tax value
        $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0 )   ? ($package_price * $site_setting->mrc_sales_tax)/100: 0;
        $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0 ) ? (($package_price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
        $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;

        return [
            'package_price'  => (int) round($package_price+$mrc_total),
            'mrc_sales_tax'  => $mrc_sales_tax,
            'mrc_adv_inc_tax'=> $mrc_adv_inc_tax,
            'mrc_total'      => $mrc_total
        ];
    }
    
    public function getParentPackageDetails($added_to_id,$pkg_id){
        return FranchisePackage::where('added_to_id',$added_to_id)->where('package_id',hashids_decode($pkg_id))->first();
    }
    //return transaction arr
    public function transactionArr($transaction_id, $admin_id, $user_id, $amount, $old_balance){

        $arr = array(
            'transaction_id'    => $transaction_id,
            'admin_id'          => $admin_id,
            'user_id'           => hashids_decode($user_id),
            'amount'            => $amount,
            'old_balance'       => $old_balance,
            'new_balance'       => ($old_balance != null) ? $old_balance - $amount : null,
            'type'              => 0,
            'created_at'        => date('Y-m-d H:i:s')
        );
        return $arr;
    }

    //check if balance or credit limit is greater or equal to the package price
    public function checkBalanceAndCredit($balance, $credit_limit, $pkg_price, $user_type){
        if($balance > 0){//check if balance is not in minus
            if((!($balance >= $pkg_price)) ){//if balance is not greater than the package price
                if(!(($credit_limit+$balance) >= $pkg_price)){//if credit_limit+balance is not greater than the package price
                    echo json_encode([
                        'error' =>  "$user_type balance is not enough"
                    ]);
                    exit();
                }
            }
        }elseif($balance <= 0 && ((abs($balance) <= $credit_limit) )){//if blanace is less then or equal to zero and and balance is less then or equal to credit limit
            if(!($credit_limit+$balance >= $pkg_price)){//if credit_limit+balance is not greater or equal to pkg_price
                echo json_encode([
                    'error' => "$user_type balance is not enough"
                ]);
                exit();
            }
        }elseif((abs($balance) >= $credit_limit)){//if credit limit exceed
            echo json_encode([
                'error' => "$user_type balance is not enough"
            ]);
            exit();
        }      
    }
    //divide the pkg price with 30 days and then multiply by given days
    public function dividePkgPrice($exp_date,$pkg_price){

        $current_date = date_create(date('Y-m-d 12:00:00'));
        $exp_date     = date_create($exp_date);

        $days =  date_diff($current_date,$exp_date)->format('%a');
        
        $divide_pkg_price = $pkg_price/30;

        return $divide_pkg_price * ($days-1);
    }
    /* 
        this function first checks if the user is franchise,dealer or subdealer after that it will check if calendar is true then make the package price accoridng to date and after that it will check if remaining_days and user_package_price is not null not null means package is about to uprade then if will calculate the remaining days and their amount and then minus it from new package amount and then return
    */
    public function franchiseNetworkPriceDeduction($package_id,$transaction_id,$user_id,$remaining_days=null,$invoice=null){
        // $user = User::where('id', hashids_decode($user_id))->first();
        // dd($user);
        if(true){//if user is franchise

            $franchise      = Admin::where('id',auth()->user()->id)->firstOrFail();//find franchise
            $franchise_pkg  = Package::findOrFail(hashids_decode($package_id));//find franchise pkg details
            if(isset($validated['calendar'])){//if calendar expiry date is set then divide the pkg price
                $franchise_pkg_cost = number_format($this->dividePkgPrice($new_exp_date,$franchise_pkg->cost));
            }else{
                $franchise_pkg_cost = $franchise_pkg->price;
            }

            // dd($franchise_pkg_cost);
            //when upgrade user package then do this
            if(!is_null($remaining_days) && !is_null($invoice)){//if both are not null then
                $user_current_pkg_cost = $this->getParentPackageDetails($franchise->id, hashids_encode($invoice)); 
                $franchise_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $user_current_pkg_cost->cost, $franchise_pkg_cost);
                // dd($franchise_pkg_cost);
            }
            // dd($franchise_pkg_cost);
            // else{
            //     $franchise_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->franchise_cost, $franchise_pkg_cost);
            // }
            // dd($franchise_pkg_cost);
            // dd($franchise_pkg_cost);
            
            $this->checkBalanceAndCredit($franchise->balance, $franchise->credit_limit, $franchise_pkg_cost, 'Franchise');//check balance and credit limit
            
            $transaction_arr[] = $this->transactionArr($transaction_id, $franchise->id, $user_id, $franchise_pkg_cost,$franchise->balance);//put franchise pkg details in arr
            // dd($franchise_pkg->cost);
            $franchise->decrement('balance',$franchise_pkg_cost); //minus cost from franchise balanace
            $franchise->save();
            
            $package_price      = $franchise_pkg->cost;
            $franchise_pkg_cost = $franchise_pkg_cost;
            $total_amount       = $franchise_pkg_cost;
        
        }elseif(auth()->user()->user_type == 'dealer'){//is user is dealer
            
            $parent_franchise = Admin::where('id',auth()->user()->added_to_id)->firstOrFail();//find franchsie
            $dealer           = Admin::where('id',auth()->user()->id)->firstOrFail();//find dealer
            $franchise_pkg    = $this->getParentPackageDetails($parent_franchise->id, $package_id);//find franchise pkg dealils
            $dealer_pkg       = $this->getParentPackageDetails($dealer->id, $package_id);//find details pkg details
           
            if(isset($validated['calendar'])){//if calendar expiry date is set then divide the pkg price
                $franchise_pkg_cost = number_format($this->dividePkgPrice($new_exp_date,$franchise_pkg->cost));
                $dealer_pkg_cost    = number_format($this->dividePkgPrice($new_exp_date,$dealer_pkg->cost));
            }else{
                $franchise_pkg_cost = $franchise_pkg->cost;
                $dealer_pkg_cost    = $dealer_pkg->cost;
            }

            //when upgrade user package then do this
            // if(!is_null($remaining_days) && !is_null($invoice)){//if both are null then 
            //     $franchise_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->franchise_cost, $franchise_pkg_cost);
            //     $dealer_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->dealer_cost, $dealer_pkg_cost);
            // }
            if(!is_null($remaining_days) && !is_null($invoice)){//if both are null then 
                // $franchise_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->franchise_cost, $franchise_pkg_cost);
                // $dealer_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->dealer_cost, $dealer_pkg_cost);
                $f_user_current_pkg_cost = $this->getParentPackageDetails($parent_franchise->id, hashids_encode($invoice)); 
                $franchise_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $f_user_current_pkg_cost->cost, $franchise_pkg_cost);
                $d_user_current_pkg_cost = $this->getParentPackageDetails($dealer->id, hashids_encode($invoice)); 
                $dealer_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $d_user_current_pkg_cost->cost, $dealer_pkg_cost);
            }

            // dd($invoice->franchise_cost);
            $this->checkBalanceAndCredit($dealer->balance, $dealer->credit_limit, $dealer_pkg_cost, 'Dealer');//check balance and credit limit
            $this->checkBalanceAndCredit($parent_franchise->balance, $parent_franchise->credit_limit, $franchise_pkg_cost, 'Franchise');//check balance and credit limit


            $transaction_arr[] = $this->transactionArr($transaction_id, $parent_franchise->id, $user_id, $franchise_pkg_cost,$parent_franchise->balance);//put franchsie pkg details in arr
            $transaction_arr[] = $this->transactionArr($transaction_id, $dealer->id, $user_id, $dealer_pkg_cost,$dealer->balance);//put dealer pkg details in arr
            
            $parent_franchise->decrement('balance',$franchise_pkg_cost);//minus pkg cost from franchise balance            
            // $parent_franchise->decrement('balance',$franchise_pkg->cost);//minus pkg cost from franchise balance
            $parent_franchise->save();
            
            $dealer->decrement('balance',$dealer_pkg_cost);//minus pkg cost from dealer balance
            $dealer->save();

            $package_price      = $dealer_pkg->cost;
            $dealer_pkg_cost    = $dealer_pkg_cost;
            $franchise_pkg_cost = $franchise_pkg_cost;
            $total_amount       = $dealer_pkg_cost;

        }elseif(auth()->user()->user_type == 'sub_dealer'){

            $subdealer        = Admin::where('id',auth()->user()->id)->firstOrFail();//find subdealer
            $parent_dealer    = Admin::where('id',$subdealer->added_to_id)->firstOrFail();//find dealer
            $parent_franchise = Admin::where('id',$parent_dealer->added_to_id)->firstOrFail();//find franchise

            $franchise_pkg    = $this->getParentPackageDetails($parent_franchise->id, $package_id); //find franchier pkg details
            $dealer_pkg       = $this->getParentPackageDetails($parent_dealer->id, $package_id);//fiind dealer pkg details
            $subdealer_pkg    = $this->getParentPackageDetails($subdealer->id, $package_id);//find sbdealer pkg details
            
            if(isset($validated['calendar'])){//if calendar expiry date is set then divide the pkg price
                $franchise_pkg_cost = number_format($this->dividePkgPrice($new_exp_date,$franchise_pkg->cost));
                $dealer_pkg_cost    = number_format($this->dividePkgPrice($new_exp_date,$dealer_pkg->cost));
                $subdealer_pkg_cost = number_format($this->dividePkgPrice($new_exp_date,$subdealer_pkg->cost));
            }else{
                $franchise_pkg_cost = $franchise_pkg->cost;
                $dealer_pkg_cost    = $dealer_pkg->cost;
                $subdealer_pkg_cost = $subdealer_pkg->cost;
            }   
            
            //when upgrade user package then do this
            // if(!is_null($remaining_days) && !is_null($invoice)){//if both are null then 
            //     $franchise_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->franchise_cost, $franchise_pkg_cost);
            //     $dealer_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->dealer_cost, $dealer_pkg_cost);
            //     $subdealer_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->subdealer_cost, $subdealer_pkg_cost);
            // }
            if(!is_null($remaining_days) && !is_null($invoice)){//if both are null then 
                // $franchise_pkg_cost = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->franchise_cost, $franchise_pkg_cost);
                // $dealer_pkg_cost    = $this->getPackagePriceByRemainingDays($remaining_days, $invoice->dealer_cost, $dealer_pkg_cost);
                $f_user_current_pkg_cost = $this->getParentPackageDetails($parent_franchise->id, hashids_encode($invoice)); 
                $franchise_pkg_cost      = $this->getPackagePriceByRemainingDays($remaining_days, $f_user_current_pkg_cost->cost, $franchise_pkg_cost);
                $d_user_current_pkg_cost = $this->getParentPackageDetails($parent_dealer->id, hashids_encode($invoice)); 
                $dealer_pkg_cost         = $this->getPackagePriceByRemainingDays($remaining_days, $d_user_current_pkg_cost->cost, $dealer_pkg_cost);
                $s_user_current_pkg_cost = $this->getParentPackageDetails($subdealer->id, hashids_encode($invoice)); 
                $subdealer_pkg_cost      = $this->getPackagePriceByRemainingDays($remaining_days, $s_user_current_pkg_cost->cost, $subdealer_pkg_cost);


            }
            
            $this->checkBalanceAndCredit($subdealer->balance, $subdealer->credit_limit, $subdealer_pkg_cost, 'Subdealer');//check balance and credit limit
            $this->checkBalanceAndCredit($parent_dealer->balance, $parent_dealer->credit_limit, $dealer_pkg_cost, 'Dealer');//check balance and credit limit
            $this->checkBalanceAndCredit($parent_franchise->balance, $parent_franchise->credit_limit, $franchise_pkg_cost, 'Franchise');//check balance and credit limit


            $transaction_arr[] = $this->transactionArr($transaction_id, $parent_franchise->id, $user_id, $franchise_pkg_cost,$parent_franchise->balance);//put franchise pkg details in arr
            $transaction_arr[] = $this->transactionArr($transaction_id, $parent_dealer->id, $user_id, $dealer_pkg_cost,$parent_dealer->balance);//put dealer pkg details in arr
            $transaction_arr[] = $this->transactionArr($transaction_id, $subdealer->id, $user_id, $subdealer_pkg_cost,$subdealer->balance);//put subdealer pkg details in arr

            $parent_franchise->decrement('balance',$franchise_pkg_cost);//minus pkg cost from franchsie balance
            $parent_franchise->save();
            
            $parent_dealer->decrement('balance',$dealer_pkg_cost);//minus pkg cost from dealer balance
            $parent_dealer->save();

            $subdealer->decrement('balance',$subdealer_pkg_cost);//minus pkg cost form subdealer
            $subdealer->save();
            
            $package_price      = $subdealer_pkg->cost;
            $subdealer_pkg_cost = $subdealer_pkg_cost;
            $dealer_pkg_cost    = $dealer_pkg_cost;
            $franchise_pkg_cost = $franchise_pkg_cost;
            $total_amount       = $subdealer_pkg_cost;


            #note franchise, dealer and subdealer same package cost could be different 
        }else{
            $all_packages = Package::get();                
            
            $admin           = Admin::where('id',auth()->user()->id)->firstOrFail();//find franchise
            $admin_pkg       = $all_packages->where('id',hashids_decode($package_id))->first();//find franchise pkg details
            $admin_pkg_cost  = $admin_pkg->price;

            // $transaction_arr[] = $this->transactionArr($transaction_id, $admin->id, $user_id, $admin_pkg_cost,null);//put franchise pkg details in arr
            $transaction_arr[] = null;

            $package_price      = $admin_pkg_cost;
            
        }
        return  array(
            'package_price'         => round($package_price),
            'subdealer_pkg_cost'    => round(@$subdealer_pkg_cost) ?? 0.00,
            'dealer_pkg_cost'       => round(@$dealer_pkg_cost) ?? 0.00,
            'franchise_pkg_cost'    => round(@$franchise_pkg_cost) ?? 0.00,
            'total_amount'          => round(@$total_amount),
            'transaction_arr'       => $transaction_arr,
        );
    }
    //this function will get the remainig days and the package price then return the new package price according to the remainig days
    public function getPackagePriceByRemainingDays($remaining_days, $user_package_price, $parent_pkg_cost){
        
        $remaining_days_amount = $user_package_price/30;
        $remaining_days_amount = $remaining_days*$remaining_days_amount;
        
        $parent_pkg_cost = $parent_pkg_cost/30; //divice pkg cost with 30
        $parent_pkg_cost = $parent_pkg_cost*$remaining_days;//multiply by remaing_days
        $parent_pkg_cost = $parent_pkg_cost-$remaining_days_amount;//minus remaing amount from pkg cost
     
        return $parent_pkg_cost;
    }
    //this funciton get the packages according to the user type while user package activation
    public function getPackages($user_type){
        $html     = '<option value="">Select package</option>';
        $packages = Package::when($user_type == 'monthly', function($query){
                        $query->whereBetween('usertype', [1,10]);
                    })->when($user_type == 'half_year', function($query){
                        $query->whereBetween('usertype', [11,20]);
                    })->when($user_type == 'full_year', function($query){
                        $query->whereBetween('usertype', [21,30]);
                    })->when($user_type == 'promo', function($query){
                        $query->whereBetween('usertype', [31,40]);
                    })->get();
        
        foreach($packages AS $package){
            $html .= "<option value='$package->hashid'>$package->name</option>";
        }
        
        return response()->json([
            'html'  => $html
        ]);
    }

    public function createExpirationDate($package_id, $user_id){//create the expiration date
        $package             = Package::findOrFail(hashids_decode($package_id));
        $user                = User::findOrFail(hashids_decode($user_id));
        $new_expiration_date = null;
        $otc                 = false;

        $site_setting           = Cache::get('edit_setting');
        //calculate the tax value
        $mrc_sales_tax          = ($site_setting->mrc_sales_tax   != 0)   ? ($package->price * $site_setting->mrc_sales_tax)/100: 0;
        $mrc_adv_inc_tax        = ($site_setting->mrc_adv_inc_tax != 0) ? (($package->price+$mrc_sales_tax) * $site_setting->mrc_adv_inc_tax)/100: 0;
        $otc_sales_tax          = ($site_setting->mrc_adv_inc_tax != 0) ? ($package->otc * $site_setting->otc_sales_tax)/100: 0;
        $otc_adv_inc_tax        = ($site_setting->otc_adv_inc_tax != 0) ? (($package->otc+$otc_sales_tax) * $site_setting->otc_adv_inc_tax)/100: 0;
        $mrc_total              = $mrc_sales_tax+$mrc_adv_inc_tax;
        $otc_total              = $otc_sales_tax+$otc_adv_inc_tax;

        if(is_null($user->current_expiration_date)){
            $new_expiration_date = now()->addMonth($package->duration)->format('d-M-Y 12:00');
        }else{
            $new_expiration_date = now()->parse($user->current_expiration_date)->addMonth($package->duration)->format('d-M-Y 12:00');
        }
        if($user->status == 'registered'){
            $otc = $package->otc;
        }

        return response()->json([
            'new_expiration_date'   => $new_expiration_date,
            'package_price'         => round($package->price+$mrc_total),
            'renew_package_name'    => $package->name,
            'otc'                   => $otc,
            'user_status'          => $user->status,
            // 'otc_total'             => $otc_total
        ]);
    }

    public function getUpgradePackagePrice(Request $req){
        $user                = User::findOrFail(hashids_decode($req->user_id));//get user
        $package             = Package::findOrFail(hashids_decode($req->package_id));//get package
        $user_invoice        = Invoice::where('user_id', $user->id)->whereIn('type', [0,1])->latest()->first();

        $current_date    = date_create(date('Y-m-d 12:00:00'));
        $exp_date        = date_create($user->current_expiration_date);
        $remaining_days  = date_diff($current_date,$exp_date)->format("%a");

        ($remaining_days == 31) ? $remaining_days = 30 : ''; //if remaining days are equal to 31 then make it 30 days
        
        $get_current_pkg_per_day_price = (int) $this->getPacakgePerDayPrice($user_invoice->total, $remaining_days);//get per day price of existig package
        $get_new_pkg_per_day_price     = (int) $this->getPacakgePerDayPrice($package->price, $remaining_days);//get per day price of new selected package      
        $new_package_price_tax_arr     =  $this->getPackagePriceWithTax($get_new_pkg_per_day_price, $remaining_days);
        $get_new_pkg_price_with_tax    = (int) $new_package_price_tax_arr['package_price'];//get total price of new package with tax
        $get_current_pkg_price         = (int) $get_current_pkg_per_day_price*$remaining_days;//get curren
        $pkg_price_to_deduct           = (int) $get_new_pkg_price_with_tax-$get_current_pkg_price;
        // $new_pkg_price_without_tax     = (int) $new_package_price_tax_arr['package_price']-$get_current_pkg_price;

        return response()->json([
            'upgrade_package_price' => $pkg_price_to_deduct,
        ]);

    }

}
