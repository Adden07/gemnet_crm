<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customize;
use Illuminate\Support\Facades\Cache;
class CustomizeController extends Controller
{
    public function index(){

        $data = array(
            'title' => 'Customize',
            'edit'  => Customize::where('admin_id',auth()->user()->id)->first(),
        );

        @$data['data'] = json_decode($data['edit']->data,true);
        
        // \CommonHelpers::activity_logs('customization');

        return view('admin.customize.index')->with($data);

    }
    //if customize_id exists then update othewise insert
    public function store(Request $req){
        $rules = [
            'top_bar'       => ['required', 'string'],
            'side_bar'      => ['required', 'string'],
            'footer'        => ['required', 'string'],
            'primary'       => ['required', 'string'],
            'primary_hover' => ['required', 'string'],
            'success'       => ['required', 'string'],
            'success_hover' => ['required', 'string'],
            'info'          => ['required', 'string'],
            'info_hover'    => ['required', 'string'],
            'warning'       => ['required', 'string'],
            'warning_hover' => ['required', 'string'],
            'danger'        => ['required', 'string'],
            'danger_hover'  => ['required', 'string'],
            'fonts'         => ['required', 'string'],
            'links'         => ['required', 'string'],
            'links_hover'   => ['required', 'string'],
        ];

        $validator = Validator::make($req->all(),$rules);
        
        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }
        //if cusomize_id does not exists then add a new entry otherwise find
        $customize = Customize::findOrNew(@hashids_decode($req->customize_id));
        $customize->admin_id = auth()->user()->id;
        $customize->data = $validator->validated();
        $customize->save();
        
        // \CommonHelpers::activity_logs('updated-customization');
        Cache::forget('customization');
        
        return response()->json([
            'success'   => 'Customization updated successfully',
            'reload'    => TRUE
        ]);
           
    }

    public function reset($id=NULL){

        if(isset($id) && !empty($id)){
            Customize::destroy(hashids_decode($id));
        }

        // \CommonHelpers::activity_logs('reseted-customization');
        Cache::forget('customization');
        
        return response()->json([
            'success'   => 'Customization Reseted Successfully',
            'reload'    => TRUE
        ]);
    }
}
