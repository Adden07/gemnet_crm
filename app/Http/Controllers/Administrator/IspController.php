<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\City;
use App\Models\Isp;

class IspController extends Controller
{
    public function index(){
        $data = array(
            'title' => 'All ISPS',
            'isps'  => Isp::with(['cities'])->latest()->get(),
        );
        
        \CommonHelpers::activity_logs('all-isps');

        return view('admin.isp.all_isps')->with($data);
    }

    public function add(){
        $data = array(
            'title' => 'Add ISP',
            'cities'    => City::latest()->get(),
        );

        \CommonHelpers::activity_logs('add-isp');

        return view('admin.isp.add_isp')->with($data);
    }

    //store and update the ISP
    public function store(Request $req){
        
        $rules = [
            'city_id'          => ['required'],
            'company_name'  => ['required', 'string', 'max:191'],
            'poc_name'      => ['required', 'string', 'max:191'], 
            'mobile'        => ['required', 'string', 'max:13']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }

        if(isset($req->isp_id) && !empty($req->isp_id)){
            $isp = Isp::findOrFail(hashids_decode($req->isp_id));
            $msg = 'Isp Updated Successfully';
            $activity_log = 'updated-isp';
        }else{
            $isp = new Isp;
            $msg = 'ISP Added Successfully';
            $activity_log = 'added-isp';
        }
        $isp.
        $isp->city_id       = hashids_decode($req->city_id);
        $isp->admin_id      = auth()->guard('admin')->user()->id;
        $isp->company_name  = ucwords($req->company_name);
        $isp->poc_name      = ucwords($req->poc_name);
        $isp->mobile        = $req->mobile;
        $isp->address       = $req->address;
        $isp->save();

        \CommonHelpers::activity_logs($activity_log);
        
        return response()->json([
            'success'       => $msg,
            'redirect'      => route('admin.isp.index')
        ]);
    }

    //edit ISP
    public function edit($id){
        if(isset($id) && !empty($id)){
            $data = array(
                'title'     => 'Edit ISP',
                'is_update' => TRUE,
                'cities'    => City::latest()->get(),
                'edit_isp'  => Isp::findOrFail(hashids_decode($id)),
            );

            \CommonHelpers::activity_logs('edit-isp');

            return view('admin.isp.add_isp')->with($data);
        }
    }
}
