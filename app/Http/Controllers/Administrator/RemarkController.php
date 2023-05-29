<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\RemarkType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RemarkController extends Controller
{
    public function index(){
        $data  = array(
            'title' => 'Remarks',
            'remarks'   => RemarkType::latest()->get(),
        );
        return view('admin.remarks.index')->with($data);
    }

    public function store(Request $req){
        $rules = [
            'remark_type'   => ['required', 'max:50', 'string'],
            'remark_id'     => ['nullable', 'string', 'max:1000']
        ];
        $validator = Validator::make($req->all(), $rules);
        $msg       = null;
        
        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        if(isset($req->remark_id) && !empty($req->remark_id)){
            RemarkType::findOrFail(hashids_decode($req->remark_id))->update(['remark_type'=>$req->remark_type]);
            $msg  = 'Remark type updated successfully';
        }else{
            RemarkType::create(['remark_type'=>$req->remark_type, 'admin_id'=>auth()->id()]);
            $msg  = 'Remark type added successfully';
        }
        return response()->json([
            'success'   => $msg,
            'redirect'  => route('admin.remarks.index')
        ]);
    }

    public function edit($id){
        $data = array(
            'title' => 'Edit Remark',
            'edit_remark'   => RemarkType::findOrFail(hashids_decode($id)),
            'remarks'   => RemarkType::latest()->get(),
            'is_update' => true
        );
        return view('admin.remarks.index')->with($data);
    }

    public function delete($id){
        RemarkType::findOrFail(hashids_decode($id));
        return response()->json([
            'success'   => 'Remark deleted successfully',
            'reload'    => true
        ]);
    }
}
