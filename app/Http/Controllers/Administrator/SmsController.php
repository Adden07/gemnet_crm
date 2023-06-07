<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use DataTables;
use Illuminate\Support\Facades\Cache;

class SmsController extends Controller
{
    public function index(){
        
        if(\CommonHelpers::rights('enabled-sms','all-sms')){
            return redirect()->route('admin.home');
        }
        $data = array(
            'title'     => 'SMS',
            'messages'  => Sms::latest()->get(),
        );
        return view('admin.sms.index')->with($data);
    }

    public function store(Request $req){
        $rules = [
            'type'    => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:200'],
            'status'  => ['required', 'string', 'in:1,0'],
            'sms_id'  => ['nullable', 'string', 'max:100']
        ];

        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }

        $validated = $validator->validated();
        $msg       = '';

        if(isset($validated['sms_id'])){
            Sms::findOrFail(hashids_decode($validated['sms_id']))->update(collect($validated)->except('sms_id')->toArray());
            $msg = 'Sms updated successfully';
        }else{
            Sms::create($validated);//inser data in table
            $msg = 'SMS added successfully';
        }

        \Cache::forget('sms_cache');//for resetting cache

        return response()->json([
            'success'  => $msg,
            'redirect' => route('admin.sms.index'),
        ]);
    }

    public function edit($id){
        $data = array(
            'title'     => 'Edit sms',
            'edit_sms'  => Sms::findOrFail(hashids_decode($id)),
            'is_update' => true,
            'messages'  => Sms::latest()->get(),
        );
        return view('admin.sms.index')->with($data);
    }

    public function delete($id){
        Sms::destroy(hashids_decode($id));
        return response()->json([
            'success'   => 'SMS deleted successfully',
            'reload'    => true
        ]);
    }

    public function manualSms(){
        if(\CommonHelpers::rights('enabled-sms','manual-sms')){
            return redirect()->route('admin.home');
        }
        $data = array(
            'title' => 'Manual SMS',
            'messages'=> SmsLog::where('is_manual',1)->get(),
        );
        return view('admin.sms.manual_sms')->with($data);
    }

    public function sendManualSMs(Request $req){
        $rules = [
            'mobile_no' => ['required', 'numeric', 'digits:12,12'],
            'message'   => ['required', 'string', 'max:1000']
        ];
        $validator = Validator::make($req->all(), $rules);
        $msg       = [];

        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }
        $validated = $validator->validated();
        
        if(CommonHelpers::sendSms($validated['mobile_no'], $validated['message']) == 'Success'){//send sms and check status
            CommonHelpers::smsLog(null,null,$validated['mobile_no'],$validated['message'],1,1);//success log
            $msg =  [
                'success' => 'Sms sent successfully',
                'reload'  => true                
            ];
        }else{
            CommonHelpers::smsLog(null,null,$validated['mobile_no'],$validated['message'],1,1);//failed log
            $msg =  [
                'error' => 'Failed to send sms',
            ];
        }
        return response()->json($msg);
    }

    public function smsByUser(){
        if(\CommonHelpers::rights('enabled-sms','sms-by-user')){
            return redirect()->route('admin.home');
        }
        $data = array(
            'title'     => 'Sms By User',
            'users'     => User::latest()->get(),
            'messages'  => SmsLog::with(['user:id,name,username'])->where('is_manual', 1)->whereNotNUll('user_id')->get(),
        );
        return view('admin.sms.sms_by_user')->with($data);
    }

    public function sendSmsByUser(Request $req){

        $rules = [
            'user_id'   => [Rule::requiredIf($req->type == 'individual'), 'string', 'max:100', 'nullable'],
            'mobile_no' => [Rule::requiredIf($req->type == 'individual'), 'numeric', 'digits:12,12', 'nullable'],
            'message'   => ['required', 'string', 'max:1000'],
            'type'      => ['required', 'in:individual,all,active,expired,terminated']
        ];
        $validator = Validator::make($req->all(), $rules);
        $msg       = [];
        
        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }
        $validated = $validator->validated();

        if($validated['type'] == 'individual'){//send message to individual user
            if(CommonHelpers::sendSms($validated['mobile_no'], $validated['message']) == 'Success'){//send sms and check status
                CommonHelpers::smsLog($validated['user_id'],null,$validated['mobile_no'],$validated['message'],1,1);//success log
                $msg =  [
                    'success' => 'Sms sent successfully',
                    'reload'  => true                
                ];
            }else{
                CommonHelpers::smsLog($validated['user_id'],null,$validated['mobile_no'],$validated['message'],0,1);//failed log
                $msg =  [
                    'error' => 'Failed to send sms',
                ];
            }
        }else{//send message to multiple users based on their status
            $users = User::when($validated['type'] != 'all', function($query) use ($validated){
                            $query->where('status', $validated['status']);
                        })->get(['id', 'status', 'mobile']);
            $counter=0;
            foreach($users AS $user){
                if(CommonHelpers::sendSms($user->mobile, $validated['message']) == 'Success'){//send sms and check status
                    CommonHelpers::smsLog(hashids_encode($user->id),null,$user->mobile,$validated['message'],1,1);//success log
                    ++$counter;
                }
            }
            $msg =[
                'success'   => "SMS sent to $counter users",
                'reload'    => true
            ];
        }

        return response()->json($msg);
    }

    public function logPage(Request $req){
        if(CommonHelpers::rights('enabled-finance','view-payments')){
            return redirect()->route('admin.home');
        }
        // $admin_ids = Admin::where('user_type','admin')->get()->pluck('id')->toArray();
        
        if($req->ajax()){

            return DataTables::of(SmsLog::with(['user']))
                                ->addIndexColumn()
                                ->addColumn('date', function($data){
                                    return date('d-M-Y H:i:s', strtotime($data->created_at));
                                })
                                ->addColumn('username', function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>hashids_encode($data->user_id)])." target='_blank'>{$data->user->username}</a>";
                                })
                                ->addColumn('sms_type', function($data){
                                    return $data->sms_type;
                                })
                                ->addColumn('mobile_no', function($data){
                                    return $data->mobile_no;
                                })
                                ->addColumn('sms', function($data){
                                    return wordwrap($data->sms, 30, "<br />\n");
                                })
                                ->addColumn('is_manual', function($data){
                                    if($data->is_manual){
                                        $is_manual = '<span class="badge badge-success">yes</span>';
                                    }else{
                                        $is_manual = '<span class="badge badge-info">No</span>';
                                    }
                                    return $is_manual;
                                })
                                ->addColumn('status', function($data){
                                    if($data->status){
                                        $status = '<span class="badge badge-success">Success</span>';
                                    }else{
                                        $status = '<span class="badge badge-danger">Failed</span>';
                                    }
                                    return $status;
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->user_id) && $req->user_id != 'all'){
                                        $query->where('user_id',hashids_decode($req->user_id));
                                    }

                                    if(isset($req->from_date) && isset($req->to_date)){
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);
                                    }

                                    if(isset($req->sms_type) && $req->sms_type != 'all'){
                                        $query->where('sms_type', $req->sms_type);
                                    }

                                    // if(isset($req->search)){
                                    //     $query->where(function($search_query) use ($req){
                                    //         $search = $req->search;
                                    //         $search_query->orWhere('created_at', 'LIKE', "%$search%")
                                    //                     ->orWhere('type', 'LIKE', "%$search%")
                                    //                     ->orWhere('amount', 'LIKE', "%$search%")
                                    //                     ->orWhere('old_balance', 'LIKE', "%$search%")
                                    //                     ->orWhere('new_balance', 'LIKE', "%$search%")
                                    //                     ->orWhereHas('receiver',function($q) use ($search){
                                    //                             $q->whereLike(['name','username'], '%'.$search.'%');

                                    //                         })
                                    //                     ->orWhereHas('admin',function($q) use ($search){
                                    //                         $q->whereLike(['name','username'], '%'.$search.'%');

                                    //                     });      
                                    //     });
                                    // }

                                    
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                ->rawColumns(['username', 'sms', 'is_manual', 'status'])
                                ->make(true);

        }
        $sms = SmsLog::groupBy(['user_id'])->get(['user_id', 'sms_type']);

        $data = array(  
            'title'     => 'Payments',
            'users'     => User::whereIn('id', $sms->pluck('user_id')->toArray())->get(),
            'sms_types' =>  $sms->pluck('sms_type')->unique(),
        );
        return view('admin.sms.sms_log')->with($data);
    }
}
