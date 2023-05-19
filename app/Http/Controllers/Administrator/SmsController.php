<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    public function index(){
        $data = array(
            'title'     => 'SMS',
            'messages'  => Sms::latest()->get(),
        );
        return view('admin.sms.index')->with($data);
    }

    public function store(Request $req){
        $rules = [
            'type'    => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:30'],
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

        Cache::forget('sms_cache');//for resetting cache

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
        $data = array(
            'title'     => 'Sms By User',
            'users'     => User::latest()->get(),
            'messages'  => SmsLog::with(['user:id,name,username'])->where('is_manual', 1)->whereNotNUll('user_id')->get(),
        );
        return view('admin.sms.sms_by_user')->with($data);
    }

    public function sendSmsByUser(Request $req){
 
        $rules = [
            'user_id'   => ['required', 'string', 'max:100'],
            'mobile_no'    => ['required', 'numeric', 'digits:12,12'],
            'message'   => ['required', 'string', 'max:1000']
        ];
        $validator = Validator::make($req->all(), $rules);
        $msg       = [];

        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }
        $validated = $validator->validated();

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
        return response()->json($msg);
    }
}
