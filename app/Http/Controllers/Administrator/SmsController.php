<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Sms;
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
}
