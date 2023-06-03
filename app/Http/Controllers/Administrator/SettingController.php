<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\City;
use App\Models\Setting;
use App\Models\Area;
use App\Models\Admin;
use App\Models\AdminAcl;

class SettingController extends Controller
{
    public function index(){
        
        if((\CommonHelpers::rights(true,'enabled-settings'))){
            return redirect()->route('admin.home');
        }
        
        $data = array(
            'title'         => 'Settings',
            'cities'        => City::get(),
            'areas'         => Area::with(['city'])->where('type','area')->get(),
            'subareas'      => Area::with(['city','area'])->where('type','sub_area')->latest()->get(),
            'admins'        => Admin::where('id', '!=', auth()->user()->id)->where('user_type', '!=', 'superadmin')->get(),
        );

        // \CommonHelpers::activity_logs('view-settings');

        return view('admin.setting.index')->with($data);
    }
    //update if setting_id is set otherwise insert a new record
    public function store(Request $req){
        
        $rules = [
            'company_name'  => ['required', 'string', 'max:191', /*Rule::unique('settings')->ignore(@hashids_decode($req->setting_id))*/],
            'slogan'        => ['required', 'string', 'max:190'],
            'mobile'        => ['required', 'numeric', 'digits:10'],
            'email'         => ['required', 'string', 'max:190', /*Rule::unique('settings')->ignore(@hashids_decode($req->setting_id))*/],
            'address'       => ['required'],
            'logo'          => ['nullable', Rule::requiredIf(!isset($req->setting_id)), 'mimes:jpg,jpeg,png', 'max:1000'],
            'favicon'       => ['nullable', Rule::requiredIf(!isset($req->setting_id)), 'mimes:jpg,jpeg,png', 'max:1000'],
            'zipcode'       => ['required', 'numeric'],
            'copyright'     => ['required', 'string', 'max:191'],
            'mrc_sales_tax' => ['required', 'numeric'],
            'mrc_adv_inc_tax' => ['required', 'numeric'],
            'otc_sales_tax'   => ['required', 'numeric'],
            'otc_adv_inc_tax' => ['required', 'numeric'],
            'sms_api_url'     => ['nullable', 'string', 'max:1000', 'url'],
            'sms_api_id'      => ['nullable', 'string', 'max:50'],
            'sms_api_pass'    => ['nullable', 'string', 'max:50'],
            'is_sms'          => ['required', 'in:1,0'],
            'ntn'             => ['nullable', 'string', 'max:50'],
            // 'srb_sales_tax'   => ['nullable', 'string', 'max:50'],
            'bank_name'    => ['nullable', 'string', 'max:50'],
            'account_title'    => ['nullable', 'string', 'max:50'],
            'account_no'    => ['nullable', 'string', 'max:50']    
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $setting                = Setting::findOrNew(@hashids_decode($req->setting_id));
        $setting->admin_id      = auth()->user()->id;
        $setting->company_name  = $req->company_name;
        $setting->email         = $req->email;
        $setting->slogan        = $req->slogan;
        $setting->mobile        = $req->mobile;
        $setting->address       = $req->address;
        $setting->country       = $req->country;
        $setting->zipcode       = $req->zipcode;
        $setting->copyright     = $req->copyright;
        $setting->mrc_sales_tax   = $req->mrc_sales_tax;
        $setting->mrc_adv_inc_tax = $req->mrc_adv_inc_tax;
        $setting->otc_sales_tax   = $req->otc_sales_tax;
        $setting->otc_adv_inc_tax = $req->otc_adv_inc_tax;
        $setting->sms_api_url = $req->sms_api_url;
        $setting->sms_api_id = $req->sms_api_id;
        $setting->sms_api_pass = $req->sms_api_pass;
        $setting->is_sms = $req->is_sms;
        $setting->ntn = $req->ntn;
        // $setting->srb_sales_tax = $req->srb_sales_tax;
        $setting->bank_name = $req->bank_name;
        $setting->account_title = $req->account_title;
        $setting->account_no = $req->account_no;

        if($req->hasFile('logo')){//store logo
            $logo = \CommonHelpers::uploadSingleFile($req->file('logo'),'admin_uploads/logo/');
            $setting->logo = $logo;
        }

        if($req->hasFile('favicon')){//store favicon
            $favicon = \CommonHelpers::uploadSingleFile($req->file('favicon'),'admin_uploads/favicon/');
            $setting->favicon = $favicon;
        }

        $setting->save();

        \CommonHelpers::activity_logs('changed settings general');

        Cache::forget('edit_setting');//for resetting cache

        return response()->json([
            'success'   => 'Setting Updated Successfully',
            'reload'    => TRUE
        ]);
    }

    //for modes
    public function mode(Request $req){
   
        $rules = [
            'full_maintenance'  => ['required', 'in:on,off'],
            'setting_id'        => ['required']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors() ];
        }

        $validated = $validator->validated();

        $setting = Setting::findOrFail(hashids_decode($req->setting_id));
        $setting->full_maintenance = $validated['full_maintenance'];
        $setting->save();

        \CommonHelpers::activity_logs("maintance mode -($req->full_maintenance)");
        
        Cache::forget('edit_setting');//for resetting cache

        return response()->json([
            'success'   => 'Maintenance  Mode Updated Successfully',
            'reload'    => TRUE
        ]);
    }

    public function editAcl($id){
        if((\CommonHelpers::rights(true,'enabled-settings'))){
            return redirect()->route('admin.home');
        }
        
        $data = array(
            'title'         => 'Settings',
            'cities'        => City::get(),
            'areas'         => Area::with(['city'])->where('type','area')->get(),
            'subareas'      => Area::with(['city','area'])->where('type','sub_area')->latest()->get(),
            'admins'        => Admin::where('id', '!=', auth()->user()->id)->where('user_type', '!=', 'superadmin')->get(),
            'edit_acl'      => AdminAcl::findOrFail(hashids_decode($id)),
        );    
        return view('admin.setting.index')->with($data);
    }

    public function deleteAcl($id){
        AdminAcl::destroy(hashids_decode($id));
        return response()->json([
            'success'   => 'Admin acl deleted successfully',
            'reload'    => true
        ]);
    }
}
