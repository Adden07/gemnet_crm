<?php

namespace App\Http\Controllers\Administrator;

use App\Exports\UpdateUserExpirationExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;
use App\Helpers\CommonHelpers;
use App\Models\City;
use App\Models\User;
use App\Models\Area;
use App\Models\UserPackageRecord;
use App\Models\Radacct;
use App\Models\RadPostAuth;
use App\Models\MacDb;
use App\Models\Admin;
use App\Models\Package;
use App\Models\FranchisePackage;
use App\Models\RadUserGroup;
use App\Models\RadCheck;
use App\Models\Invoice;
use App\Models\RadacctArchive;
use DB;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Models\UserTmp;
use App\Exports\UserTmpExport;
use App\Exports\UpdateUserExport;
use App\Imports\UpdateUserExpirationImport;
use App\Imports\UpdateUserImport;
use App\Models\FileLog;
use App\Models\Remark;
use App\Models\Remarks;

class UserController extends Controller
{   
    public function __constrct(){
        ini_set('memory_limit', '384M');
    }


    public function index(Request $req){

        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }
        $user_package_ids = null;

        if(auth()->user()->user_type != 'admin'){ //when user is not admin then get the current packages id by using group by
            $user_package_ids = User::select('package')
                                    ->when(auth()->user()->user_type != 'admin', function($query){
                                        $query->whereIn('admin_id',$this->getChildIds());

                                    })
                                    ->groupBY('package')
                                    ->get()
                                    ->pluck('package')
                                    ->toArray();
        }                        

        if($req->ajax()){

            $search = $req->search;
            
            $data = User::selectRaw('id,name,username,status,user_status,last_logout_time,current_expiration_date,mobile,package')
                        ->when(auth()->user()->user_type != 'admin', function($query){
                            // $query->whereIn('admin_id',$this->getChildIds());
                        });

            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name',function($data){
                                    return wordwrap($data->name,10,"<br>\n");
                                })
                                ->addColumn('username',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>$data->hashid])." target='_blank'>$data->username</a>";
                                })
                                ->addColumn('mobile',function($data){
                                    return $data->mobile;
                                })
                                // ->addColumn('sales_person',function($data){
                                //     return wordwrap($data->admin->name."(<strong>".$data->admin->username."</strong>)",10,"<br>\n");
                                // })
                                ->addColumn('package',function($data){
                                    return wordwrap(@$data->primary_package->name,10,"<br>\n");
                                })
                                ->addColumn('status',function($data){
                                    $status = '';
                                    if($data->user_status != 0 && $data->user_status != 2){
                                        if($data->status == 'registered'){
                                            $status = '<span class="badge badge-info">Registered</span>';
                                        }elseif($data->status == 'active' && $data->last_logout_time != null && date('Y',strtotime($data->last_logout_time)) == '1970'){
                                            $status = '<span class="badge badge-success">Active</span>-<span class="badge badge-warning">Never Online</span>';
                                        }elseif($data->status == 'expired' && $data->last_logout_time != null && date('Y',strtotime($data->last_logout_time)) != '1970'){
                                            $status = '<span class="badge badge-danger">Expired</span>-<span class="badge badge-danger">Offline</span>';
                                        }elseif($data->status == 'expired' && $data->last_logout_time != null && date('Y',strtotime($data->last_logut_time)) == '1970'){
                                            $status = '<span class="badge badge-danger">Expired</span>-<span class="badge badge-warning">Never Online</span>';
                                        }elseif($data->status == 'active' && $data->last_logout_time == null){
                                            $status = '<span class="badge badge-success">Active</span>-<span class="badge badge-success">Online</span>';
                                        }elseif($data->status == 'active' && $data->last_logout_time != null){
                                            $status = '<span class="badge badge-success">Active</span>-<span class="badge badge-danger">Offline</span>';
                                        }elseif($data->status == 'expired' && $data->last_logout_time == null){
                                            $status = '<span class="badge badge-danger">Expired</span>-<span class="badge badge-success">Online</span>';
                                        }else{
                                            $status = '<span class="badge badge-danger">Expired</span>';
                                        }
                                    }elseif($data->user_status == 2){
                                        $status = '<span class="badge badge-danger">Disabled By Admin</span>';
                                    }else{
                                        $status = '<span class="badge badge-danger">Disabled</span>';
                                    }
                                    return $status;
                                })
                                ->addColumn('expiration',function($data){
                                    $status = '';
                                    if($data->status == 'active'){
                                        $status = "<span class='badge badge-success'>".$data->current_expiration_date."</span>";
                                    }else{
                                        $status = "<span class='badge badge-danger'>".$data->current_expiration_date."</span>";
                                    }
                                    return $status;
                                })
                                ->addColumn('action',function($data){
                                $action = '';
                                
                                if(auth()->user()->can('edit-user')){
                                    $action = "<a href=".route('admin.users.edit',['id'=>$data->hashid])." class='btn btn-warning btn-xs waves-effect waves-light' title='Edit'>
                                            <i class='icon-pencil'></i>
                                           </a>";
                                }
                                // if($data->admin_id == auth()->user()->id){
                                    if($data->status == 'registered'){
                                        if(auth()->user()->can('active-user')){
                                            $action .= " <a href=".route('admin.packages.add_user_package',['id'=>$data->hashid])." class='btn btn-primary btn-xs add_package ml-2' title='Activate Package' data-status='".$data->status."' onclick=\"addPackage('".$data->hashid ."','".$data->status ."',event)\">
                                                        <i class='icon-plus'></i>
                                                    </a>";
                                        }
                                    }else{ 
                                        if(auth()->user()->can('renew-user'))    
                                            $action .= "<a href=".route('admin.packages.add_user_package',['id'=>$data->hashid])." class='btn btn-primary btn-xs add_package ml-2' title='Renew Package' data-status=".$data->status." onclick=\"addPackage('".$data->hashid."','".$data->status."',event)\">
                                                <i class='icon-refresh'></i>
                                            </a>";
                                    }
                                //}
                                return $action;           
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->status)){
                                        if($req->status == 'active'){
                                            $query->where('status','active')->where('user_status',1);
                                        }elseif($req->status == 'expired'){
                                            $query->where('status','expired')->where('user_status',1);
                                        }elseif($req->status == 'active_never_online'){
                                            $query->where('status','active')->whereNotNull('last_logout_time')->whereYear('last_logout_time','1970')->where('user_status',1);
                                        }elseif($req->status == 'active_online'){
                                            $query->where('status','active')->whereNull('last_logout_time')->where('user_status',1);
                                        }elseif($req->status == 'active_offline'){
                                            $query->where('status','active')->whereNotNull('last_logout_time')->whereYear('last_logout_time','!=', '1970')->where('user_status',1);
                                        }elseif($req->status == 'expired_never_online'){
                                            $query->where('status','expired')->whereNotNull('last_logout_time')->whereYear('last_logout_time','1970')->where('user_status',1);
                                        }elseif($req->status == 'expired_online'){
                                            $query->where('status','expired')->whereNull('last_logout_time')->where('user_status',1);
                                        }elseif($req->status == 'expired_offline'){
                                            $query->where('status','expired')->whereNotNull('last_logout_time')->whereYear('last_logout_time','!=', '1970')->where('user_status',1);
                                        }elseif($req->status == 'registered'){
                                            $query->where('status', 'registered')->where('user_status',1);
                                        }elseif($req->status == 'disabled'){
                                            $query->where('user_status',0);
                                        }
                                    }
                                    $query->when(isset($req->from_date) && isset($req->to_date) && $req->expiration_date == 'all',function($query) use ($req){
                                        $query->whereDate('current_expiration_date', '>=', date('Y-m-d',strtotime($req->from_date)))
                                              ->whereDate('current_expiration_date', '<=', date('Y-m-d',strtotime($req->to_date)));
                                    })->when(isset($req->package_id),function($query) use ($req){
                                        $query->Where('c_package',hashids_decode($req->package_id));
                                    });

                                    if(isset($req->expiration_date) && $req->expiration_date != 'all'){
                                        if(date('d', strtotime($req->expiration_date)) == date('d')){
                                            $query->whereDate('current_expiration_date', $req->expiration_date);
                                        }elseif(date('d', strtotime($req->expiration_date)) == date('d',strtotime('+1 days'))){
                                            $query->whereDate('current_expiration_date', $req->expiration_date);
                                        }elseif(date('d', strtotime($req->expiration_date)) == date('d',strtotime('+3 days'))){
                                            $query->whereDate('current_expiration_date', '>=', date('Y-m-d'))
                                                ->whereDate('current_expiration_date', '<', $req->expiration_date);
                                        }elseif(date('d', strtotime($req->expiration_date)) == date('d',strtotime('+1 week'))){
                                            $query->whereDate('current_expiration_date', '>=', date('Y-m-d'))
                                                ->whereDate('current_expiration_date', '<', $req->expiration_date);
                                        }
                                    }
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->whereLike([
                                                        'name',
                                                        'username',
                                                        'status',
                                                        'user_status',
                                                        'last_logout_time',
                                                        'current_expiration_date',
                                                        'mobile'
                                                    ], 
                                            $search)
                                            ->orWhereHas('admin', function($q) use ($search) {
                                                $q->whereLike(['name','username'], '%'.$search.'%');
                                            });
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                })
                                ->rawColumns(['sales_person','status','expiration','action','name','username','package'])
                                ->toJson();
        }
        
        $data = array(
            'title' => 'All Users',
            
            'users_count'   => User::select('status')->when(auth()->user()->user_type != 'admin',function($query){
                                        // $query->whereIn('admin_id',$this->getChildIds());
                                    })->when(isset($req->user_status),function($query) use ($req){
                                        // $query->where('status',$req->user_status);
                                    })->get(),

            'packages'    => Package::withCount(['users'])
                                    ->orderBy('id','DESC')->get(),

            'franchises'  => Admin::where('user_type','franchise')->latest()->get(),
            
            'user_type'   => auth()->user()->user_type,      
        );

        // if(auth()->user()->user_type != 'admin'){
        //     if(auth()->user()->user_type == 'franchise'){
        //         $data['childs'] = Admin::where('added_to_id',auth()->user()->id)->where('user_type','dealer')->get();
        //     }elseif(auth()->user()->user_type == 'dealer'){
        //         $data['childs'] = Admin::where('added_to_id',auth()->user()->id)->where('user_type','sub_dealer')->get();
        //     }
        // }

        //for user count when user is not admin
        if(auth()->user()->user_type != 'admin'){
            // $data['user_count'] = User::select(['admin_id','package'])->whereIn('admin_id',CommonHelpers::getChildIds())->get(); 
            $data['user_count'] = 0;
        }
   

        return view('admin.user.all_users')->with($data);
    }
    //add user
    public function add(){
        // CommonHelpers::sendSms();        
        if(CommonHelpers::rights('enabled-user','add-user')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title'     => 'Add User',
            'cities'    => City::get(),
            'areas'     => Area::where('area_id',0)->latest()->get(),
            'user_types' => Admin::whereIn('user_type', ['field_engineer','sales_person'])->get(),
        );
        
        return view('admin.user.add_user')->with($data);
    }

    //store and update the user
    public function store(Request $req){
        // dd($req->all());
        $rules = [
            'city_id'           => ['required'],
            'name'              => [Rule::requiredIf($req->user_type != 'company'), 'string', 'max:50', 'nullable'],
            'comp_name'         => [Rule::requiredIf($req->user_type == 'company'), 'string', 'max:50', 'nullable'],
            'username'          => ['required', 'string', 'min:1', 'max:14'],
            'password'          => [Rule::requiredIf(!isset($req->user_id)), 'nullable', 'min:6', 'max:12', 'confirmed'],
            'nic'               => [Rule::requiredIf($req->user_type == 'individual'), 'string', 'min:15', 'max:15', 'nullable'],
            'mobile'            => [Rule::requiredIf($req->user_type != 'company'), 'numeric', 'nullable'],
            'comp_mobile'       => [Rule::requiredIf($req->user_type == 'company'), 'numeric', 'nullable'],
            'address'           => ['required', 'string' ],
            'nic_front'         => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'nic_back'          => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'user_form_front'   => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'user_form_back'    => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            'area_id'           => ['nullable'],
            'subarea_id'        => ['nullable'],
            'is_tax'            => [Rule::requiredIf(auth()->user()->user_type == 'admin'), 'nullable'],
            'sales_id'          => ['required', 'string', 'max:100'],
            'fe_id'             => ['required', 'string', 'max:100'],
            'user_type'         => ['required', 'string', 'in:company,individual'],
            'business_name'     => [Rule::requiredIf($req->user_type == 'company'), 'string', 'max:100', 'nullable'],
            'ntn'               => [Rule::requiredIf($req->user_type == 'company'), 'string', 'max:100', 'nullable'],
            'landline_no'       => ['string', 'nullable']
        ];
        
        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        //check useranme existss
        if(User::where('username',auth()->user()->username.'-'.$req->username)->where('id','!=',@hashids_decode($req->user_id))->exists()){
            return response()->json([
                'error' => 'Username Already Exists',
            ]);
        }

        if(isset($req->user_id)){
            $user       = User::findOrFail(hashids_decode($req->user_id));
            $msg        = 'User Updated Successfully';
            $activity   = "updated-user";
        }else{
            $user              = new User;
            // $user->admin_id    = auth()->user()->id;
            // $user->user_status = 'registered';
            $msg               = 'user Added Successfully';
            $activity          = "added-user";
        }


        //nic front
        if($req->hasFile('nic_front')){
            $nic_front       = CommonHelpers::uploadSingleFile($req->nic_front, 'admin_uploads/nic_front/');
            $user->nic_front = $nic_front;
        }
        //nic back
        if($req->hasFile('nic_back')){
            $nic_back       = CommonHelpers::uploadSingleFile($req->nic_back, 'admin_uploads/nic_back/');
            $user->nic_back = $nic_back;
        }
        //user form front
        if($req->hasFile('user_form_front')){
            $user_form_front       = CommonHelpers::uploadSingleFile($req->user_form_front, 'admin_uploads/user_form_front/');
            $user->user_form_front = $user_form_front;
        }
         //user form back
         if($req->hasFile('user_form_back')){
            $user_form_back       = CommonHelpers::uploadSingleFile($req->user_form_back, 'admin_uploads/user_form_back/');
            $user->user_form_back = $user_form_back;
        }
        $user->activation_by = auth()->id();
        $user->city_id     = @hashids_decode($req->city_id);    
        $user->area_id     = @hashids_decode($req->area_id);
        $user->subarea_id  = @hashids_decode($req->subarea_id);
        $user->name        = $req->name ?? $req->comp_name;
        $user->username    = (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin') ? $req->username : auth()->user()->username.'-'.$req->username;
        $user->password    = (!empty($req->password)) ? $req->password : $user->password;
        $user->nic         = $req->nic;
        $user->mobile      = ($req->user_type != 'company') ? '92'.$req->mobile : '92'.$req->comp_mobile;
        $user->address     = $req->address;
        $user->is_tax      = (isset($req->is_tax)) ? (int) $req->is_tax : 1;
        $user->sales_id    = hashids_decode($req->sales_id);
        $user->fe_id       = hashids_decode($req->fe_id);
        $user->user_type   = $req->user_type;
        $user->ntn         = @$req->ntn;
        $user->business_name = @$req->business_name;
        $user->landline_no   = @$req->landline_no;
        $user->save();

        CommonHelpers::activity_logs($activity.' '.$user->username);

        return response()->json([
            'success'     => $msg,
            'redirect'    => route('admin.users.index'),
        ]);
    }

    //edit user
    public function edit($id){
        
        if(CommonHelpers::rights('enabled-user','edit-user')){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data = array(
                'title'     => 'Edit User',
                'cities'    => City::get(),
                'edit_user' => User::findOrFail(hashids_decode($id)),
                'is_update' => TRUE,
                'user_types' => Admin::whereIn('user_type', ['field_engineer','sales_person'])->get(),

            );

            if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin'){
                $data['areas']    = Area::where('city_id',$data['edit_user']->city_id)->where('type','area')->get();
                $data['subareas'] = Area::where('area_id',$data['edit_user']->area_id)->get();
            }else{
                $data['areas']    = auth()->user()->areas()->where('type','area')->get();
                $data['subareas'] = auth()->user()->areas()->where('type','sub_area')->get()->where('area_id',$data['edit_user']->area_id);
            }

            CommonHelpers::activity_logs('edit-user');

            return view('admin.user.add_user')->with($data);
        }
    }
    //check unique value of specified columna
    public function checkUnique(Request $req){
        if(isset($req->column) && isset($req->value) && !empty($req->column) && !empty($req->value)){
            $user = User::when($req->column == 'username' && auth()->user()->user_type != 'admin',function($query) use ($req){
                $query->where('username',auth()->user()->username.'-'.$req->value);
            })->when($req->column == 'username' && auth()->user()->user_type == 'admin',function($query) use ($req){
                $query->where('username',$req->value);
            })->when($req->column == 'mobile',function($query) use ($req){
                // $query->where($req->column,$req->value);
                $query->where(function($query) use ($req){
                    $query->select(DB::raw('COUNT(*)'))
                          ->from('users')
                          ->where($req->column, $req->value);
                }, '>', 4);
            })->when($req->column == 'nic',function($query) use ($req){
                $query->where(function($query) use ($req){
                    $query->select(DB::raw('COUNT(*)'))
                          ->from('users')
                          ->where($req->column, $req->value);
                }, '>', 4);
            })->when(isset($req->id),function($query) use ($req){
                $query->where('id','!=',hashids_decode($req->id));
            })->first();

            if(!empty($user)){
                echo "false";
                die();
            }
            
            echo "true";
            die();
        }
    }
    //dispaly details in modal
    public function details($id){
        
        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $details = User::with(['admin','city'])->findOrFail(hashids_decode($id));
            $html    = view('admin.user.details_modal')->with(compact('details'))->render();
            
            return response()->json([
                'html'  => $html
            ]);
        }
        abort(404);
    }

    //remove attachments only for admins
    public function removeAttachment(Request $req){

        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        if(isset($req->id) && isset($req->type) && isset($req->path)){
            
            $remove = User::findOrFail(hashids_decode($req->id));

            if($req->type == 'nic_front'){
                $remove->nic_front = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'nic_back'){
                $remove->nic_back = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'user_form_front'){
                $remove->user_form_front = null;
                @unlink(public_path($req->path));
            }elseif($req->type == 'user_form_back'){
                $remove->user_form_back = null;
                @unlink(public_path($req->path));
            }

            $remove->save();
            
            CommonHelpers::activity_logs('remove-user-attachments');

            return response()->json([
                'success'   => 'Attachment Removed Successfully',
                'reload'    => TRUE
            ]);
        }
        abort(404);
    }

    //subareas list
    public function subareas($id){
        if(isset($id) && !empty($id)){
            // if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin'){
            //     $subareas = Area::where('area_id',hashids_decode($id))->where('type','sub_area')->get();
            // }else{
            //     $subareas = auth()->user()->areas()->where('type','sub_area')->get()->where('area_id',hashids_decode($id));
            // }
            $subareas = Area::where('area_id', hashids_decode($id))->get();
            $html = view('admin.user.subarea_list',compact('subareas'))->render();

            return response()->json([
                'html'  => $html,
            ]);
        }
        abort(404);
    }
    //dispaly user profile
    public function profile($id, $remark_id=null){
        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data = array(
                'title' => 'User Profile',
                'user_details'  => User::with(['user_package_record','user_package_record.package', 'admin','city','area','subarea','primary_package','current_package','lastPackage', 'activation', 'renew','remark.admin'])->findORFail(hashids_decode($id)),
                'user_records'  => UserPackageRecord::with(['package','admin','user','last_package'])->where('user_id',hashids_decode($id))->latest()->get(),
                'cities'        => City::get(),
                'user_invoices' => Invoice::select(['id', 'invoice_id', 'created_at','current_exp_date','new_exp_date','pkg_id','user_id','paid', 'pkg_price', 'total'])
                                            ->with(['package'=>function($query){
                                                $query->select('id','name');
                                            },'user'=>function($query){
                                                $query->select('id','paid');
                                            }])
                                            ->where('user_id',hashids_decode($id))
                                            ->when(auth()->user()->user_type != 'admin',function($query){
                                                $query->where('admin_id',auth()->user()->id);
                                            })
                                            ->latest()->limit(6)->get(),
                'packages'      =>      Package::get(),
                'areas'         =>      Area::latest()->get(),
            );
            if($remark_id != null){
                $data['edit_remark'] = Remarks::findOrFail(hashids_decode($remark_id));
            }
            // dd($data['user_details']->remark);
            //update user last profile visit column
            User::where('id',hashids_decode($id))->update(['last_profile_visit_time'=>date('Y-m-d H:i:s')]);
            
            return view('admin.user.user_profile')->with($data);
        }
    }

    //update user password
    public function updatePassword(Request $req){
        
        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        $rules = [
            'password'  => ['required', 'min:6', 'max:12', 'confirmed'],
            'user_id'   => ['required']
        ];

        $validator = Validator::make($req->all(),$rules);
        $validated = $validator->validated();

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $user = User::findOrFail(hashids_decode($validated['user_id']));
        $user->password = $validated['password'];
        $user->save();
        //update password in radcheck
        $radcheck = RadCheck::where('username',$user->username)->where('attribute','Cleartext-Password')->firstOrFail();
        $radcheck->value = $validated['password'];
        $radcheck->save();
        
        CommonHelpers::activity_logs("changed password-($user->username)");
        
        return response()->json([
            'success'   => 'User password Updated Successfully',
            'redirect'    => route('admin.users.profile',['id'=>$validated['user_id']])
        ]);
    }

    //update user images
    public function updateDocument(Request $req){

        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        if(isset($req->user_id) && !empty($req->user_id)){

            $rules = [
                'nic_front'         => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
                'nic_back'          => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
                'user_form_front'   => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
                'user_form_back'    => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2000'],
            ];

            $validator = Validator::make($req->all(),$rules);

            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }

            $user = User::findOrFail(hashids_decode($req->user_id));
            
            //nic front
            if($req->hasFile('nic_front')){
                $nic_front = CommonHelpers::uploadSingleFile($req->nic_front, 'admin_uploads/nic_front/');
                $user->nic_front = $nic_front;
                @unlink($req->old_nic_front);
            }
            //nic back
            if($req->hasFile('nic_back')){
                $nic_back = CommonHelpers::uploadSingleFile($req->nic_back, 'admin_uploads/nic_back/');
                $user->nic_back = $nic_back;
                @unlink($req->old_nic_back);
            }
            //user form front
            if($req->hasFile('user_form_front')){
                $user_form_front = CommonHelpers::uploadSingleFile($req->user_form_front, 'admin_uploads/user_form_front/');
                $user->user_form_front = $user_form_front;
                @unlink($req->old_user_form_front);
            }
            //user form back
            if($req->hasFile('user_form_back')){
                $user_form_back = CommonHelpers::uploadSingleFile($req->user_form_back, 'admin_uploads/user_form_back/');
                $user->user_form_back = $user_form_back;
                @unlink($req->old_user_form_back);
            }
            
            $user->save();

            CommonHelpers::activity_logs('update-documents');

            return response()->json([
                'success'   => 'Document Udpated Successfully',
                'reload'    => True
            ]);
        }    
    }

    //update user personal info
    public function updateInfo(Request $req){

        if(CommonHelpers::rights('enabled-user','view-user')){
            return redirect()->route('admin.home');
        }

        if(isset($req->user_id) && !empty($req->user_id)){
            $rules = [
                'name'              => ['required', 'string', 'max:50'],
                'nic'               => ['required', 'string', 'min:15', 'max:15'],
                'mobile'            => ['required', 'numeric', 'digits:10'],
                'address'           => ['required', 'string' ],
                'city_id'           => ['required'],
                'area_id'           => ['nullable'],
                'subarea_id'        => ['nullable']
            ];

            $validator = Validator::make($req->all(),$rules);

            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }
            
            
            $user       = User::findOrFail(hashids_decode($req->user_id));
            $msg        = 'User Personal Info Updated Successfully';
            $activity   = 'updated-personal-info';
            


            $user->city_id     = hashids_decode($req->city_id);
            $user->area_id     = @hashids_decode($req->area_id) ?? null;
            $user->subarea_id  = @hashids_decode($req->subarea_id) ?? null;
            $user->name        = $req->name;
            $user->nic         = $req->nic;
            $user->mobile      = '92'.$req->mobile;
            $user->address     = $req->address;
            $user->save();

            CommonHelpers::activity_logs($activity);

            return response()->json([
                'success'   => $msg,
                'redirect'    => route('admin.users.index'),
            ]);
        }
        abort(404);
    }
    //all online users
    public function onlineUsers(Request $req){

        if(CommonHelpers::rights('enabled-user','online-users')){
            return redirect()->route('admin.home');
        }

        //get users of login user
        $users = User::whereIn('admin_id',$this->getChildIds())->get()->pluck('username')->toArray();
        
        if($req->ajax()){
            // ini_set('memory_limit', '2000M');
            
            $data = Radacct::with(['user'=>function($query){
                                $query->select(['id', 'name', 'username']);
                            }])
                            ->select(['radacctid', 'acctstarttime', 'username', 
                            'callingstationid', 'framedipaddress' ,'acctinputoctets', 
                           'acctoutputoctets'])
                            ->when(auth()->user()->user_type != 'admin',function($query) use ($users){
                                $query->whereIn('username',$users);
                            })
                            ->whereNull('acctstoptime');

            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name',function($data){
                                    return wordwrap($data->user->name,10,"<br />\n");
                                })
                                ->addColumn('username',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>hashids_encode($data->user->id)])." target='_blank'>".wordwrap($data->user->username,10,"<br>\n",true)."</a>";
                                })
                                ->addColumn('login',function($data){
                                    return date('d-M-Y H:i:s',strtotime($data->acctstarttime));
                                })
                                ->addColumn('uptime',function($data){
                                    $date1 = date_create($data->acctstarttime);
                                    $date2 = date_create(date('Y-m-d H:i:s'));

                                    $dateDifference = date_diff($date1, $date2)->format('%ad %Hh %im %ss');

                                    return $dateDifference;
                                })
                                ->addColumn('mac_address',function($data){
                                    return $data->callingstationid;
                                })
                                ->addColumn('ip_address',function($data){
                                    return $data->framedipaddress;
                                })
                                ->addColumn('up',function($data){
                                    return "<span class='badge badge-primary'>".number_format($data->acctinputoctets/pow(1024,3),2)."GB</span>";
                                })
                                ->addColumn('down',function($data){
                                    return "<span class='badge badge-primary'>".number_format($data->acctoutputoctets/pow(1024,3),2)."GB</span>";
                                })
                                ->addColumn('kick',function($data){
                                    return "<a href='#' class='btn btn-danger btn-sm' onclick='ajaxRequest(this)' data-url=".route('admin.users.kick',['id'=>hashids_encode($data->user->id)])." title='kick user'>
                                                kick
                                            </a>";
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->status)){
                                        if($req->status == 'active'){
                                            $query->where('framedipaddress','not like','172%');
                                        }elseif($req->status == 'expired'){
                                            $query->where('framedipaddress','like','172%');
                                        }
                                    }
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->orWhere('username', 'LIKE', "%$search%")
                                                        // ->orWhere('name', 'LIKE', "%$search%")
                                                        ->orWhere('acctstarttime', 'LIKE', "%$search%")
                                                        ->orWhere('callingstationid', 'LIKE', "%$search%")
                                                        ->orWhere('framedipaddress', 'LIKE', "%$search%")
                                                    ->orWhereHas('user',function($q) use ($search){
                                                            $q->whereLike(['name','username'], '%'.$search.'%');

                                                        });      
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('radacctid', $o);
                                    })
                                ->rawColumns(['name','username','up','down','kick'])
                                ->make(true);
        }
        $data = array(
            'title' => 'Online Users',
            'radaccts'  => Radacct::with(['user:id'])
                                ->select(['framedipaddress'])
                                ->when(auth()->user()->user_type != 'admin',function($query) use ($users){
                                    $query->whereIn('username',$users);
                                })
                                ->whereNull('acctstoptime')
                                ->get(),
        );
        // dd($data['radaccts'][0]);
        return view('admin.user.online_users')->with($data);
    }

    //all ofline users
    public function oflineUsers(Request $req){
        
        if(CommonHelpers::rights('enabled-user','offline-users')){
            return redirect()->route('admin.home');
        }
        $time = null;

        if(isset($req->time)){
            if($req->time == 24){
                $time = date('Y-m-d',strtotime('+1 day'));
            }    
        }

        $data = User::select(['id', 'name', 'username', 'current_expiration_date', 'last_login_time', 'last_logout_time', 'address'])
                        ->when(auth()->user()->user_type != 'admin',function($query){
                            $query->whereIn('admin_id',$this->getChildIds());
                        })
                        ->where('status','active')
                        ->whereYear('last_logout_time','!=',1970)
                        ->whereNotNUll('last_logout_time');
                        // ->orderBy('current_expiration_date','ASC');
        
        if($req->ajax()){
            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('username', function($data){
                                    return "<a href='".route('admin.users.profile',['id'=>hashids_encode($data->id)])."' target='_blank'>".wordwrap($data->username,10,"<br >\n",true)."<a/>";
                                })
                                ->addColumn('remarks',function($data){
                                    return wordwrap($data->remarks, 20, "<br />\n", true);
                                })
                                ->addColumn('name', function($data){
                                    return wordwrap($data->name,10,"<br />\n",true);
                                })
                                ->addColumn('address', function($data){
                                    return wordwrap($data->address,20,"<br />\n", true);
                                })
                                ->addColumn('expiration', function($data){
                                    return date('d-M-Y H:i:s',strtotime($data->current_expiration_date));
                                })
                                ->addColumn('last_login', function($data){
                                    $last_login = '';
                                    if(date('Y-m-d',strtotime($data->last_login_time)) == date('Y-m-d'))
                                        $last_login = "<span class='badge badge-success'>$data->last_login_time</span>";
                                    elseif(date('Y-m-d',strtotime($data->last_login_time)) == date('Y-m-d',strtotime('-1 day')))
                                        $last_login = "<span class='badge badge-warning'>$data->last_login_time</span>";
                                    else
                                        $last_login = "<span class='badge' style='background-color:orangered'>$data->last_login_time</span>";
                                    
                                    return $last_login;

                                })
                                ->addColumn('last_logout', function($data){
                                    $last_logout = '';
                                    if(date('Y-m-d',strtotime($data->last_logout_time)) == date('Y-m-d')){
                                        $last_logout = "<span class='badge badge-success'>$data->last_logout_time</span>";
                                    }elseif(date('Y-m-d',strtotime($data->last_logout_time)) == date('Y-m-d',strtotime('-1 day'))){
                                        $last_logout = "<span class='badge badge-warning'>$data->last_logout_time</span>";
                                    }else{
                                        $last_logout = "<span class='badge' style='background-color:orangered'>$data->last_logout_time</span>";
                                    }
                                    return $last_logout;
                                })
                                ->filter(function($query) use ($req){

                                    if(isset($req->last_logout_time) && !empty($req->last_logout_time)){
                                        $query->whereDate('last_logout_time', '>=', $req->last_logout_time)->whereDate('last_logout_time', '<=', date('Y-m-d H:i:s'));

                                    }

                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->orWhere('username', 'LIKE', "%$search%")
                                                         ->orWhere('name', 'LIKE', "%$search%")
                                                         ->orWhere('address', 'LIKE', "%$search%")
                                                         ->orWhere('current_expiration_date', 'LIKE', "%$search%")
                                                         ->orWhere('last_login_time', 'LIKE', "%$search%")
                                                         ->orWhere('last_logout_time', 'LIKE', "%$search%");
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex',function($query,$order){
                                    $query->orderBy('current_expiration_date',$order);
                                })
                                // ->whiteList(['name','username'])
                                ->rawColumns(['username', 'name', 'address', 'expiration', 'last_login', 'last_logout', 'remarks'])
                                // ->make(true);
                                ->toJson();

        }
        //get users of login user
        $data = array(
            'title' => 'Ofline Users',
            'users' => User::when(auth()->user()->user_type != 'admin',function($query){
                            $query->whereIn('admin_id',$this->getChildIds());
                        })
                        ->when(isset($req->time),function($query) use ($req){
                            $time ='';
                        })
                        ->where('status','active')
                        ->whereYear('last_logout_time','!=',1970)
                        ->whereNotNUll('last_logout_time')->orderBy('current_expiration_date','ASC')->latest()->paginate(300),

        );
        return view('admin.user.ofline_users')->with($data);
    }


    //display all failed logins
    public function loginFailLogs(Request $req){
        if(CommonHelpers::rights('enabled-user','login-fail-users')){
            return redirect()->route('admin.home');
        }



        //get users of login user
        $users = User::get()->pluck('username')->toArray();

        if($req->ajax()){
            
            $data =  RadPostAuth::select(['authdate', 'username', 'pass', 'reply', 'mac', 'nasipaddress'])
                                ->when(auth()->user()->user_type != 'admin',function($query) use ($users){
                                    $query->whereIn('username',$users);
                                })
                                ->orderBy('id','DESC');

            return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('dateTime',function($data){
                            return date('d-M-Y H:i:s',strtotime($data->authdate));
                        })
                        ->addColumn('username',function($data){
                            return $data->username;
                        })
                        ->addColumn('pass',function($data){
                            return $data->pass;
                        })
                        ->addColumn('reason',function($data){
                            $reason = '';
                            if($data->reply == 'Access-Reject - ')
                                $reason = "<span class='badge badge-dark'> $data->reply </span>";
                            elseif($data->reply == 'Access-Reject - Wrong-Pass')
                                $reason = "<span class='badge badge-danger'> $data->reply </span>";
                            elseif($data->reply == 'Access-Reject - Wrong Mac Address')
                                $reason = "<span class='badge badge-primary'> $data->reply </span>";
                            elseif($data->reply == 'Access-Reject - User is Disabled')
                                $reason = "<span class='badge badge-success'> $data->reply </span>";
                            elseif($data->reply == 'Access-Reject - User Already Online')    
                                $reason = "<span class='badge badge-secondary'> $data->reply </span>";    
                            else
                                $reason = "<span class='badge badge-warning'> $data->reply </span>"; 

                            return $reason;
                        })
                        ->addColumn('mac',function($data){
                            return $data->mac;
                        })
                        ->addColumn('nas',function($data){
                            return $data->nasipaddress;
                        })
                        ->filter(function($query) use ($req){
                            if(isset($req->reasons) && !empty($req->reasons)){
                                $query->where('reply','LIKE',"%$req->reasons%");
                            }

                            if(isset($req->search)){
                                $query->where(function($search_query) use ($req){
                                    $search = $req->search;
                                    $search_query->orWhere('username', 'LIKE', "%$search%")
                                                 ->orWhere('authdate', 'LIKE', "%$search%")
                                                 ->orWhere('pass', 'LIKE', "%$search%")
                                                 ->orWhere('authdate', 'LIKE', "%$search%")
                                                 ->orWhere('mac', 'LIKE', "%$search%")
                                                 ->orWhere('reply', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->rawColumns(['reason'])
                        ->make(true);
        }

        $data = array(
            'title' => 'Login Fail Logs',
            'login_fail_logs'   => RadPostAuth::when(auth()->user()->user_type != 'admin',function($query) use ($users){
                                                    $query->whereIn('username',$users);
                                                })->orderBy('id','DESC')->paginate(300),
            'reasons'           =>  RadPostAuth::select('reply')->groupBy('reply')->get(),       
        );  
        
        return view('admin.user.login_fail_logs')->with($data);
    }

    //display all mac vendor users
    public function macVendorUsers(Request $req){
        if(CommonHelpers::rights('enabled-user','mac-vendor-users')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
            $data = User::with(['admin'])
                        ->when(auth()->user()->user_type != 'admin',function($query){
                                $query->whereIn('admin_id',$this->getChildIds());
                            })
                            ->whereNotNull('macvendor');

            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name',function($data){
                                    return $data->name;
                                    // return wordwrap($data->name,10,"<br>\n");

                                })
                                ->addColumn('username',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>$data->hashid])." target='_blank'>$data->username</a>";
                                })
                                ->addColumn('macaddress',function($data){
                                    return @$data->macaddress;
                                })
                                ->addColumn('sales_person',function($data){
                                    if($data->admin->user_type == 'franchise'){
                                        return "<a href=".route('admin.franchises.profile',['id'=>hashids_encode($data->admin->id)])." target='_blank'>{$data->admin->username}</a>";
                                    }elseif($data->admin->user_type == 'dealer'){
                                        return "<a href=".route('admin.dealers.profile',['id'=>hashids_encode($data->admin->id)])." target='_blank'>{$data->admin->username}</a>";
                                    }elseif($data->admin->user_type == 'sub_dealer'){
                                        return "<a href=".route('admin.sub_dealers.profile',['id'=>hashids_encode($data->admin->id)])." target='_blank'>{$data->admin->username}</a>";
                                    }
                                    
                                })
                                ->addColumn('mac_vendor',function($data){
                                    return @$data->macvendor;
                                })
                                ->addColumn('status',function($data)
                                {   
                                    $status = '';
                                    
                                    if($data->status == 'active')
                                    $status = "<span class='badge badge-success'>Active</span></span>";
                                    else 
                                    $status = "<span class='badge badge-danger'>Expired</span></span>";

                                    return $status;
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->vendor)){
                                        $query->where('macvendor',$req->vendor);
                                    }
                                    
                                    if(isset($req->status) && $req->status != 'all'){
                                        $query->where('status', $req->status);
                                    }

                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
    
                                            $search_query->whereLike([
                                                        'name',
                                                        'username',
                                                        'macaddress',
                                                        'macvendor',
                                                        'status',
                                                        'current_expiration_date',
                                                        'mobile'
                                                    ], 
                                            $search)
                                            ->orWhereHas('admin', function($q) use ($search) {
                                                $q->whereLike(['name','username'], '%'.$search.'%');
                                            });
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                })
                                ->rawColumns(['sales_person','status','expiration','view_user','action','name','status', 'username'])
                                ->make(true);
                                // ->toJson();
        }
        $data = array(
            'title' => 'Mac Vendor Users',
            'macvendors'    => DB::table('users')
                                ->select(DB::raw('count(id) as total, macvendor'))
                                ->when(auth()->user()->user_type != 'admin',function($query){
                                    $query->whereIn('admin_id',$this->getChildIds());
                                })
                                ->whereNotNull('macvendor')
                                ->groupBy('macvendor')
                                ->orderBy('total','DESC')
                                ->get(),
            'macdbs'        => MacDb::get(),                    
        );
        // dd($this->getChildIds());
        // dd($data['macvendors']);

        return view('admin.user.mac_vendor_user')->with($data);
    }

    public function getChildIds(){//get child of login admin
        
        $ids = array();
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
    //kick user
    public function kickUser($id){
        if(isset($id) && !empty($id)){
            if(CommonHelpers::kick_user_from_router($id)){
                $message = [
                    'success'   => 'User Kicked Successfully',
                    'reload'    => true,
                ];
            }else{
                $message = [
                    'error' => 'Something wrong',
                ];
            }
            return response()->json($message);
        }
        abort(404);
    }
    //chagne user status

    public function changeStatus($id,$status){
        if(in_array($status,[0,1,2])){//check if status is or 1
            if(isset($id) && !empty($id)){//if id is set
                if($status == 0 || $status == 2){//only kick user when user status is disabled
                    CommonHelpers::kick_user_from_router($id);//kick user
                }
                $user = User::findOrFail(hashids_decode($id));
                $user->user_status = (auth()->user()->user_type == 'admin' && $status == 0) ? 2 : $status;
                $user->save();

                $user_status = null; 
                if($status == 0){
                    $user_status = 'disabled';
                }elseif($status == 1){
                    $user_status = 'enabled';
                }elseif($status == 2){
                    $user_status = 'disabled by admin';
                }
                
                CommonHelpers::activity_logs("change-user-status ($user->username - $user_status)");

                return response()->json([
                    'success'   => 'User Status Updated Successfully',
                    'reload'    => TRUE,
                ]);
            }
        }
        abort(404);
    }

    //remove user mac
    public function removeMac($id){
        if(isset($id) && !empty($id)){
            $user = User::findOrFail(hashids_decode($id));
            $user->last_macaddress = $user->macaddress;
            $user->macaddress = null;
            $user->last_macvendor = $user->macvendor;
            $user->macvendor = null;
            (auth()->user()->user_type != 'admin') ? $user->decrement('macs') : '';
            $user->save();
            
            CommonHelpers::activity_logs("remove address-($user->username)");

            return response()->json([
                'success'   => 'User Mac Removed Successfully',
                'reload'    => TRUE,
            ]);
        }
        abort(404);
    }

    //reest qouta
    public function resetQouta($id){
        if(isset($id) && !empty($id)){
            $user = User::with(['primary_package'])->findOrFail(hashids_decode($id));//find user
            $user_qt_expired = $user->qt_expired;
            if($user->qt_expired == 1){
                $user->qt_expired = 0;
                $user->c_package  = $user->package;//replace current package with primay package
                //update raduesrgroup table because we have updated the package
                RadUserGroup::where('username',$user->username)->update(['groupname'=>$user->primary_package->groupname]);
            }
            $user->qt_used = 0;
            $user->save();
            
            if(is_null($user->last_login_time) || $user_qt_expired == 1){//kick user if user if online or qt_expired is 1
                CommonHelpers::kick_user_from_router($id);
            }
            
            CommonHelpers::activity_logs('reset-user-qouta');

            return response()->json([
                'success'   => 'Qouta Rest Successfully',
                'reload'    => TRUE,
            ]);
        }
    }

    public function remarks(Request $req){
        
        $validate = $req->validate([
            'remark'   => ['required', 'max:250'],
            'user_id'  => ['required'],
            'remark_id'=> ['nullable']
        ]);

        if(auth()->user()->user_type != 'admin' && is_null($req->remark_id)){//admin users can remarks twice a day except admin
            if(Remarks::where('admin_id',auth()->id())->where('user_id', hashids_decode($req->user_id))->whereDate('created_at',now())->count() >= 2){
                return response()->json([
                    'error' => 'Remark Limit Exceeded For Today !',
                ]);
            }
        }
        if(isset($req->remark_id)){
            $remark = Remarks::findOrFail(hashids_decode($req->remark_id));
            $msg    = 'Remark updated successfully';
        }else{
            $remark = new Remarks();
            $msg    = 'Remark added successfully';
        }
        $remark->admin_id  = auth()->id();
        $remark->user_id   = hashids_decode($req->user_id);
        $remark->text      = $req->remark;
        $remark->save();
        // $user = User::findOrFail(hashids_decode($req->user_id));
        // $user->remarks = $req->remark;
        // $user->save();

        return response()->json([
            'success'   => $msg,
            'redirect'  => route('admin.users.profile',['id'=>$req->user_id]),
        ]);
    }

    public function deleteRemark($id){
        Remarks::destroy(hashids_decode($id));
        return response()->json([
            'success'   => 'Remark deleted successfully',
            'reload'    => true
        ]);
    }
    

    //display loign details
    public function loginDetail(Request $req){
        if(CommonHelpers::rights('enabled-user','user-login-detail')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
     
            $data = RadacctArchive::whereDate('acctstarttime', '>=', date('Y-m-d',strtotime($req->from_date)))
                                ->whereDate('acctstoptime', '<=', date('Y-m-d',strtotime($req->to_date)))
                                ->when(auth()->user()->user_type == 'admin', function($query) use ($req){
                                    if($req->type == 'username' && $req->username != null){
                                        $query->where('username', $req->username);
                                    }elseif($req->type == 'ip' && $req->ip != null){
                                        $query->where('framedipaddress', $req->ip);
                                    }elseif($req->type == 'mac_address' && $req->macaddress != null){
                                        $query->where('callingstationid', $req->macaddress);
                                    }else{
                                        $query->where('username', $req->username);
                                    }
                                }, function($query) use ($req){
                                    if(isset($req->username)){
                                        $query->where('username', $req->username);
                                    }
                                });
                                // ->get();
                                // dd($data);

                                 

            
            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('username',function($data){
                                    return wordwrap($data->username,10,"<br>\n",true);

                                })
                                ->addColumn('dc_reason',function($data){
                                    return $data->acctterminatecause;
                                })
                                ->addColumn('login',function($data){
                                    return date('d-M-Y H:i:s',strtotime($data->acctstarttime));
                                })
                                ->addColumn('logoff',function($data){
                                    return date('d-M-Y H:i:s',strtotime($data->acctstoptime));
                                })
                                ->addColumn('uptime',function($data){

                                    $date1 = date_create($data->acctstarttime);
                                    $date2 = date_create($data->acctstoptime);

                                    $dateDifference = date_diff($date1, $date2)->format('%ad %Hh %im %ss');

                                    return $dateDifference;
                                })
                                ->addColumn('macaddress',function($data){
                                    return $data->callingstationid;
                                })
                                ->addColumn('ip',function($data){
                                    return $data->framedipaddress;
                                })
                                ->addColumn('upload', function($data){
                                    return "<span class='badge badge-primary'>".number_format($data->acctinputoctets/pow(1024,3),2)."GB</span>";
                                })
                                ->addColumn('download', function($data){
                                    return "<span class='badge badge-primary'>".number_format($data->acctoutputoctets/pow(1024,3),2)."GB</span>";
                                })
                                ->addColumn('total', function($data){
                                    return "<span class='badge badge-primary'>".number_format($data->acctoutputoctets/pow(1024,3) + $data->acctinputoctets/pow(1024,3) ,2)."GB</span>";
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
    
                                            $search_query->whereLike([
                                                        'name',
                                                        'username',
                                                        'macaddress',
                                                        'macvendor',
                                                        'status',
                                                        'current_expiration_date',
                                                        'mobile'
                                                    ], 
                                            $search)
                                            ->orWhereHas('admin', function($q) use ($search) {
                                                $q->whereLike(['name','username'], '%'.$search.'%');
                                            });
                                        });
                                    }
                                })
                                // ->orderColumn('DT_RowIndex', function($q, $o){
                                //     $q->orderBy('created_at', $o);
                                // })
                                ->rawColumns(['login', 'logoff', 'username', 'uptime', 'upload', 'download', 'total'])
                                ->make(true);
        }
        $data = array(
            'title' => 'Login Details',
            'users' => User::when(auth()->user()->user_type != 'admin',function($query){
                // $query->where('admin_id',$this->getChildIds());
            })->get(),
        );
        return view('admin.user.login_details')->with($data);
    }

    public function getPackageCount(Request $req){

        $data = User::with(['admin:id,username,name','primary_package:id,name'])
            ->selectRaw('id,admin_id,status,user_status,last_logout_time,current_expiration_date,package')
                ->when(isset($req->status) && $req->status != null, function($query) use ($req){
                    if($req->status == 'active'){
                        $query->where('status','active')->where('user_status',1);
                    }elseif($req->status == 'expired'){
                        $query->where('status','expired')->where('user_status',1);
                    }elseif($req->status == 'active_never_online'){
                        $query->where('status','active')->whereNotNull('last_logout_time')->whereYear('last_logout_time','1970')->where('user_status',1);
                    }elseif($req->status == 'active_online'){
                        $query->where('status','active')->whereNull('last_logout_time')->where('user_status',1);
                    }elseif($req->status == 'active_offline'){
                        $query->where('status','active')->whereNotNull('last_logout_time')->whereYear('last_logout_time','!=', '1970')->where('user_status',1);
                    }elseif($req->status == 'expired_never_online'){
                        $query->where('status','expired')->whereNotNull('last_logout_time')->whereYear('last_logout_time','1970')->where('user_status',1);
                    }elseif($req->status == 'expired_online'){
                        $query->where('status','expired')->whereNull('last_logout_time')->where('user_status',1);
                    }elseif($req->status == 'expired_offline'){
                        $query->where('status','expired')->whereNotNull('last_logout_time')->whereYear('last_logout_time','!=', '1970')->where('user_status',1);
                    }elseif($req->status == 'registered'){
                        $query->where('status', 'registered')->where('user_status',1);
                    }elseif($req->status == 'disabled'){
                        $query->where('user_status',0);
                    }
                })->when(isset($req->from_date) && isset($req->to_date) && $req->expiration_date == 'all',function($query) use ($req){
                        $query->whereDate('current_expiration_date', '>=', date('Y-m-d',strtotime($req->from_date)))
                              ->whereDate('current_expiration_date', '<=', date('Y-m-d',strtotime($req->to_date)));
                })->when(isset($req->package_id),function($query) use ($req){
                    $query->Where('c_package',hashids_decode($req->package_id));
                })->when(isset($req->expiration_date) && $req->expiration_date != 'all', function($query) use ($req){
                    $query->whereDate('current_expiration_date', $req->expiration_date);
                })->get();
                
                $packages = Package::withCount(['users'=>function($query) use ($data){
                                        $query->whereIn('id', $data->pluck('id')->toArray());
                                    }])
                                    ->orderBy('id','DESC')
                                    ->whereIn('id',$data->pluck('package')->unique()->toArray())
                                    ->get(); 
                
                $html = '<option value="">Select Package</option>';

                foreach($packages AS $package){
                    $html .= "<option value=$package->hashid>$package->name($package->users_count)</option>";
                }
                return response()->json([
                    'packages'  => $html,
                ]);
                
    }
    

    public function userSearch(Request $req){
        if(CommonHelpers::rights('enabled-user','search-user')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
            
            $search = $req->search;
            
            // $data = User::when(auth()->user()->user_type != 'admin', function($query){
            //                 $query->whereIn('admin_id',$this->getChildIds());
            //             });
            $data = User::query();
            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name',function($data){
                                    return wordwrap($data->name,15,"<br>\n");
                                })
                                ->addColumn('username',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>$data->hashid])." target='_blank'>$data->username</a>";
                                })
                                ->addColumn('mobile',function($data){
                                    return $data->mobile;
                                })
                                ->addColumn('nic',function($data){
                                    return $data->nic;
                                })
                                ->addColumn('address',function($data){
                                    return wordwrap($data->address,20,"<br>\n", true);
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->whereLike([
                                                        'name',
                                                        'username',
                                                        'mobile',
                                                        'nic',
                                                        'address'
                                                    ], 
                                            $search);
                                        });
                                    }
                                })
                                ->rawColumns(['username', 'name', 'address'])
                                ->toJson();
        }
        $data = array(
            'title' => 'Search User'
        );
        return view('admin.user.search_users')->with($data);
    }

    //update users functions 

    //show update users page
    public function updateUsers(Request $req){
        if($req->ajax()){
            $data = UserTmp::where('task_type', 'update-user');
            
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('task_id', function($data){
                                return $data->task_id;
                            })
                            ->addColumn('task_datetime', function($data){
                                return date('d-M-Y', strtotime($data->task_datetime));
                            })
                            ->addColumn('name', function($data){
                                return $data->name;
                            })
                            ->addColumn('username', function($data){
                                return "<a href='javascript:void(0)' route='".route('admin.users.import_modal', ['id'=>$data->hashid])."' class='users_form'>".$data->username."</a>";
                            })
                            ->addColumn('password', function($data){
                                return $data->password;
                            })
                            ->addColumn('nic', function($data){
                                return $data->nic;
                            })
                            ->addColumn('mobile', function($data){
                                return $data->mobile;
                            })
                            ->addColumn('address', function($data){
                                return wordwrap($data->address, 20, "<br>\n");
                            })
                            ->addColumn('package', function($data){
                                return @$data->packages->name;
                            })
                            ->addColumn('expiration', function($data){
                                return date('d-m-Y', strtrime($data->expiration));
                            })
                            ->addColumn('city', function($data){
                                return @$data->city->city_name;
                            })
                            ->addColumn('status', function($data){
                                $status = '';
                                if($data->result == 0){
                                    $status = '<span class="badge badge-danger">Pending</span>';
                                }elseif($data->result == 1){
                                    $status = '<span class="badge badge-success">Passed</span>';
                                }elseif($data->result == 2){
                                    $status = '<span class="badge badge-warning">Failed</span>';
                                }
                                return $status;
                            })
                            ->filter(function($query) use ($req){
                                if(isset($req->search)){
                                    $query->where(function($search_query) use ($req){
                                        $search = $req->search;

                                        $search_query->whereLike([
                                                    'task_id',
                                                    'name',
                                                    'username',
                                                    'nic',
                                                    'mobile',
                                                    'address',
                                                    'expiration'
                                                ], 
                                        $search)
                                        ->orWhereHas('packages', function($q) use ($search) {
                                            $q->whereLike(['groupname'], '%'.$search.'%');
                                        });
                                    });
                                }
                            })
                            ->orderColumn('DT_RowIndex', function($q, $o){
                                $q->orderBy('id', $o);
                            })
                            ->rawColumns(['task_datetime', 'address', 'expiration', 'status', 'username'])
                            // ->rawColumns(['login', 'logoff', 'username', 'uptime', 'upload', 'download', 'total'])
                            ->make(true);
        }
        $data = array(
            'title'     => 'Update Users',
            'admins'    => Admin::withCount(['users'])->whereNotIn('user_type', ['admin', 'superadmin'])->get(),
        );
        return view('admin.user.update_users')->with($data);
    }

    public function exportUpdateUsers(Request $req){//export users from users table
        //file name will be the username of select id
        $admin = Admin::where('id', hashids_decode($req->admin_id))->first();
        $file_name = $admin->username.'.xlsx';
        return Excel::download(new UpdateUserExport($req->admin_id, $file_name), $file_name);
    }
    //import update users from excel
    public function importUpdateUserExcel(Request $req){

        $rules = [
            'excel_file'    => ['required', 'file', 'mimes:xlsx'],
            'admin_id'       => ['required'],
        ];  

        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        DB::transaction(function() use ($req){//using DB transaction when exception through it will roll back the rows
            $import = new UpdateUserImport(hashids_decode($req->admin_id));
            //import users form excel and the insert in database
            Excel::import($import, $req->file('excel_file'));
            //check if exporting and importing user count is not equal then through error
            $file = FileLog::where('admin_id', hashids_decode($req->admin_id))->first();
            if(!empty($file)){
                if(intval($file->total_users) != $import->getRowCount()){
                    throw new \Exception('Users count does not match export count '.$file->total_users.' import count '.$import->getRowCount());
                }
            }
        });
        return response()->json([
            'success'   => 'Users imported successfully',
            'reload'    => true,
        ]);
    }

    public function updateExpiration(Request $req, $user_id){
        
        $user = User::findOrFail(hashids_decode($user_id));
        $user->status = 'active';
        $user->last_expiration_date = $user->current_expiration_date;
        $user->current_expiration_date = date('Y-m-d 12:00', strtotime($req->expiration_date));
        $user->save();

        $rad_check = RadCheck::where('username',$user->username)->where('attribute','Expiration')->first();
        
        $rad_check->value = date('d M Y 12:00',strtotime($req->expiration_date));;
        $rad_check->save();

        return response()->json([
            'success'   => 'User expiration updated successfully',
            'reload'    => true
        ]);
    }
    //user expiration using excel
    public function updateUsersExpiration(Request $req){
        if($req->ajax()){
            $data = UserTmp::with(['packages', 'city', 'admin'])->where('task_type', 'expiry-update')->where('task_complete', 0);
            
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('check_box', function($data){
                                return "<input type='checkbox' class='delete_check_box' value='".hashids_encode($data->id)."'>";
                            })
                            ->addColumn('task_id', function($data){
                                return $data->task_id;
                            })
                            ->addColumn('admin', function($data){
                                return $data->admin->username;
                            })
                            ->addColumn('task_datetime', function($data){
                                return date('d-M-Y H:i:s', strtotime($data->task_datetime));
                            })
                            ->addColumn('username', function($data){
                                return "<a href='javascript:void(0)' route='".route('admin.users.update_user_expiration_modal', ['id'=>$data->hashid])."' class='users_form'>".$data->username."</a>";
                            })
                            ->addColumn('expiration', function($data){
                                return date('d-M-Y', strtotime($data->expiration));
                            })
                            ->addColumn('new_expiration', function($data){
                                return ($data->new_expiration != null) ?  date('d-M-Y', strtotime($data->new_expiration)) : null;
                            })
                            ->filter(function($query) use ($req){
                                if(isset($req->search)){
                                    $query->where(function($search_query) use ($req){
                                        $search = $req->search;

                                        $search_query->whereLike([
                                                    'task_id',
                                                    'name',
                                                    'username',
                                                    'nic',
                                                    'mobile',
                                                    'address',
                                                    'expiration'
                                                ], 
                                        $search)
                                        ->orWhereHas('packages', function($q) use ($search) {
                                            $q->whereLike(['groupname'], '%'.$search.'%');
                                        });
                                    });
                                }
                            })
                            ->orderColumn('DT_RowIndex', function($q, $o){
                                $q->orderBy('id', $o);
                            })
                            ->rawColumns(['task_datetime', 'address', 'expiration', 'status', 'username', 'check_box'])
                            // ->rawColumns(['login', 'logoff', 'username', 'uptime', 'upload', 'download', 'total'])
                            ->make(true);
        }
        
        $data = array(
            'title'     => 'All users expiration',
            'cities'    => City::get(),
            'task_ids'  => UserTmp::groupBy('task_id')->where('task_type', 'expiry-update')->where('task_complete', 0)->get(),
            'admins'    => Admin::withCount(['users'])->whereNotIn('user_type', ['admin', 'superadmin'])->get(),
            // 'user_tmp_admins'=> UserTmp::groupBy('admin_id')->where('task_type', 'expiry-update')->get(),
        );
        return view('admin.user.update_users_expiration')->with($data);
    }

        //import update users expiration from excel
        public function importUpdateUserExpirationExcel(Request $req){
            $rules = [
                // 'excel_file'    => ['required', 'file', 'mimes:xlsx'],
                'admin_id'      => ['required'],
                'user_status'   => ['required', 'in:all,expired,active']
            ];  
    
            $validator = Validator::make($req->all(), $rules);
    
            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }

            //import users form excel and the insert in database
            // Excel::import(new UpdateUserExpirationImport(hashids_decode($req->admin_id)), $req->file('excel_file'));
            //transfer users of specifed id from users table to users_tmp table and  only transfer those users which are not already in users_tmp table
            $users = User::where('admin_id', hashids_decode($req->admin_id))
                        ->when($req->user_status != 'all', function($query) use ($req){
                            $query->where('status', $req->user_status);
                        })
                        ->get();
            
            $task_id = UserTmp::orderBy('id', 'DESC')->first();

            ($task_id == null) ? $task_id = 1 : $task_id = $task_id->task_id + 1; 

            $user_tmp = array();
            $tranfer_users_count = 0;
            $failed_users_count   = 0;
            
            foreach($users AS $key=>$user){
                if(UserTmp::where('username', $user->username)->where('task_type', 'expiry-update')->where('task_complete',0)->doesntExist()){
                    ++$tranfer_users_count;
                    $user_tmp[$key] = array(
                        'task_id'   => $task_id,
                        'task_datetime' => date('Y-m-d H:i:s'),
                        'task_type'     => 'expiry-update',
                        'admin_id'      => hashids_decode($req->admin_id),
                        'name'          => $user->name,
                        'username'      => $user->username,
                        'expiration'    => date('Y-m-d', strtotime($user->current_expiration_date)),

                    );
                }else{
                    ++$failed_users_count;
                }
            }
            UserTmp::insert($user_tmp);
            return response()->json([
                'success'   => 'Users expiration imported successfully '.$tranfer_users_count.' users transfered '.$failed_users_count.' failde',
                'reload'    => true,
            ]);
    }

    // public function validateUpdateUserExpiration(Request $req){
        
    //     if(isset($req->task_id) && !empty($req->task_id)){
    //         $users = UserTmp::where('task_id', $req->task_id)->where('task_type', 'expiry-update')->get();//get all users of specified task_id and task type is expiry-update
    //         $validation_arr = array();
            
    //         $rules = [//set rules
    //             'new_expiration'  => ['required']
    //         ];

    //         $failed_user_ids = array();
    //         $passed_user_ids = array();
    //         $user_errors     = array();
    //         $user_arr       = array();

    //         foreach($users AS $key=>$user){
    //             $validator = Validator::make($user->toArray(),$rules); //validate each row
    //             if($validator->fails()){ //if valdiation fails then store the errors
    //                 $user_errors[$key] = Arr::flatten($validator->errors()->toArray());
    //                 $failed_user_ids[] = $user->id;
    //                 //update users table
    //                 UserTmp::where('id', $user->id)->update(['errors'=>json_encode($user_errors[$key]), 'validaiton_checks'=>json_encode($validation_arr)]);//update user_tmp table
    //             }else{
    //                 $passed_user_ids[] = $user->id;//store passed user ids
    //             }
    //         }

    //         if(!empty($failed_user_ids)){ //if there is a single row which doesn't pass the test then update users_tmp table and return errors
    //             UserTmp::whereIn('id', $failed_user_ids)->update(['result'=> 2]);
    //             UserTmp::whereIn('id', $passed_user_ids)->update(['result'=> 1,'errors'=>null, 'validaiton_checks'=>null]);
    //             return response()->json([
    //                 'error' => 'Data is not valid please make changes',
    //                 'reload' => True,
    //             ]);
    //         }else{
    //             UserTmp::whereIn('id', $passed_user_ids)->update(['result'=> 1, 'errors'=>null, 'validaiton_checks'=>null]);
    //             return response()->json([
    //                 'success'   => 'Users expiration validated successfully',
    //                 'reload'    => true
    //             ]);            
    //         }
    //     }
    // }

    //export users from user_tmp table
    public function exportUpdateUserExpiration(Request $req){
        return Excel::download(new UpdateUserExpirationExport(hashids_decode($req->task_id)), 'users.xlsx');
    }

    public function updateUserExpirationModal($id){//display the form for import users

        $user = UserTmp::findOrFail(hashids_decode($id));
        $errors = ($user->errors != null) ? json_decode($user->errors) : null;
        $html =  view('admin.user.update_user_expiration_modal')->with(compact('user', 'errors'))->render();
        
        return response()->json([
            'html'  => $html
        ]);
    }

        //this function update the update user expiration
    public function updateUserExpiration(Request $req){
        // dd($req->all());
        $user              = UserTmp::findOrFail(hashids_decode($req->user_id));
        $validation_checks = json_decode($user->validaiton_checks, true);        

        $rules = [
            'username'       => ['required', 'exists:users'],
            'expiration'     => ['required', 'date'],
            'new_expiration' => ['required', 'date']
        ];


        $validator = Validator::make($req->all(),$rules);
        
        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        
        // $user->username   = $req->username;
        // $user->expiration = date('Y-m-d', strtotime($req->expiration));
        $user->new_expiration = date('Y-m-d', strtotime($req->new_expiration));
        $user->errors     = null;
        $user->save();

        return response()->json([
            'success'   => 'User updated successfully',
            'reload'    => true,
        ]);
    }



    //this function checks if all users are passed shows import otherwise valdiate
    public function checkUpdateUserExpirationStatus($id){
        if(isset($id) && !empty($id)){
            $status = UserTmp::where('task_id', $id)->whereIn('result', [0, 2])->exists();            
            $status = ($status == 'true') ? 'validate' : 'import';
            $total_users = UserTmp::where('task_id', $id)->where('result', 1)->count();

            return response()->json([
                'status' => $status,
                'total_users' => $total_users
            ]);
        }

        abort(404);
    }

    public function deleteImportUserExpiration(Request $req){//this will delete the users form tmp table
        $rules = [
            'task_id'   => ['required'],
            'type'      => ['required', 'string', 'in:delete']
        ];

        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        UserTmp::where('task_id', $req->task_id)->delete();

        return response()->json([
            'success'   => 'Update users expiration deleted successfully',
            'reload'    => true,
        ]);
    }

    public function migrateUpdateUserExpiration(Request $req){//this function will perform validation and migration  at once instead of individual functions
        
        if(isset($req->task_id) && !empty($req->task_id)){
            
            $users = UserTmp::where('task_id', $req->task_id)->where('task_type', 'expiry-update')->get();//get all users of specified task_id and task type is expiry-update
            $validation_arr = array();
            
            $rules = [//set rules
                'new_expiration'  => ['required']
            ];

            $failed_user_ids = array();
            $passed_user_ids = array();
            $user_errors     = array();

            foreach($users AS $key=>$user){
                $validator = Validator::make($user->toArray(),$rules); //validate each row
                if($validator->fails()){ //if valdiation fails then store the errors
                    $user_errors[$key] = Arr::flatten($validator->errors()->toArray());
                    $failed_user_ids[] = $user->id;
                    //update users table
                    UserTmp::where('id', $user->id)->update(['errors'=>json_encode($user_errors[$key]), 'validaiton_checks'=>json_encode($validation_arr)]);//update user_tmp table
                }else{
                    $passed_user_ids[] = $user->id;//store passed user ids
                }
            }

            if(!empty($failed_user_ids)){ //if there is a single row which doesn't pass the test then update users_tmp table and return errors
                UserTmp::whereIn('id', $failed_user_ids)->update(['result'=> 2]);
                UserTmp::whereIn('id', $passed_user_ids)->update(['result'=> 1,'errors'=>null, 'validaiton_checks'=>null]);
                return response()->json([
                    'error' => 'Data is not valid please make changes',
                    'reload' => True,
                ]);
            }
        

            // $tmp_users = UserTmp::where('task_id', $req->task_id)->get();
            $count     = 0;

            foreach($users AS $tmp){
                //update users table
                $user                           = User::where('username', $tmp->username)->first();
                $user->last_expiration_date     = $tmp->expiration ;
                $user->current_expiration_date  = date('Y-m-d 12:00', strtotime($tmp->new_expiration));
                $user->status                   = 'active';
                $user->save();
                //update radcheck table
                $rad_check = RadCheck::where('username',$user->username)->where('attribute','Expiration')->first();
                $rad_check->value = date('d M Y 12:00',strtotime($tmp->new_expiration));;
                $rad_check->save();   
                ++$count;
            }
            //update tmp_users table column task_complete as 1
            UserTmp::where('task_id', $req->task_id)->update(['task_complete'=>1]);

            return response()->json([
                'success'   => $count.' users transfered successfully',
                'reload'    => true
            ]);
        }
        abort(404);
    }

    public function deleteMultipleUpdateUserExpiration(Request $req){
        $ids = explode(',', $req->user_ids);
        $ids = array_map('hashids_decode', $ids);
        UserTmp::whereIn('id', $ids)->delete();

        return response()->json([
            'success'   => 'Users deleted successfully',
            'reload'    => true
        ]);
    }

    public function updateUsersExpirationHistory(Request $req){
        if($req->ajax()){
            $data = UserTmp::with(['admin'])->where('task_type', 'expiry-update')->where('task_complete', 1)->groupBy('task_id');
            
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('task_id', function($data){
                                return "<a href='".route('admin.users.update_users_expiration_task_history', ['task_id'=>$data->task_id])."' target='_blank'>$data->task_id</a>";
                            })
                            ->addColumn('task_datetime', function($data){
                                return date('d-M-Y H:i:s', strtotime($data->task_datetime));
                            })
                            ->filter(function($query) use ($req){
                                if(isset($req->history_admin_id)){
                                    $query->where('admin_id', hashids_decode($req->history_admin_id));
                                }
                                
                                if(isset($req->search)){
                                    $query->where(function($search_query) use ($req){
                                        $search = $req->search;

                                        $search_query->whereLike([
                                                    'task_id',
                                                ], 
                                        $search);
                                    });
                                }
                            })
                            ->orderColumn('DT_RowIndex', function($q, $o){
                                $q->orderBy('id', $o);
                            })
                            ->rawColumns(['task_datetime', 'task_id'])
                            ->make(true);
        }
    }

    public function updateUsersExpirationTaskHistory(Request $req){
        if($req->ajax()){
            // dd($req->all());
            $data = UserTmp::with(['user'])->where('task_type', 'expiry-update')->where('task_complete', 1)->where('task_id',$req->task_id);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('task_id', function($data){
                                return $data->task_id;
                            })
                            ->addColumn('username', function($data){
                                
                                // return "<a href='javascript:void(0)' route='".route('admin.users.update_user_expiration_modal', ['id'=>$data->hashid])."' class='users_form'>".$data->username."</a>";
                                return "<a href=".route('admin.users.profile',['id'=>$data->user->hashid])." target='_blank'>$data->username</a>";
                            })
                            ->addColumn('expiration', function($data){
                                return date('d-M-Y', strtotime($data->expiration));
                            })
                            ->addColumn('new_expiration', function($data){
                                return ($data->new_expiration != null) ?  date('d-M-Y', strtotime($data->new_expiration)) : null;
                            })
                            ->filter(function($query) use ($req){
                                if(isset($req->history_admin_id)){
                                    $query->where('admin_id', hashids_decode($req->history_admin_id));
                                }
                                
                                if(isset($req->search)){
                                    $query->where(function($search_query) use ($req){
                                        $search = $req->search;

                                        $search_query->whereLike([
                                                    'task_id',
                                                    'username',
                                                    // 'mobile',
                                                ], 
                                        $search);
                                        // ->orWhereHas('admin', function($q) use ($search) {
                                        //     $q->whereLike(['username'], '%'.$search.'%');
                                        // });
                                    });
                                }
                            })
                            ->orderColumn('DT_RowIndex', function($q, $o){
                                $q->orderBy('id', $o);
                            })
                            ->rawColumns(['task_datetime', 'username'])
                            ->make(true);
        }
        $data = array(
            'title' => 'Task History',
            'task'  => UserTmp::where('task_id', $req->task_id)->first(),
        );
        return view('admin.user.update_users_expiration_task_history')->with($data);
    }

    public function getUserCurrentBalance($id){
        $user = User::findOrFail(hashids_decode($id));
        return response()->json([
            'user'  => $user->user_current_balance
        ]);
    }

    public function updateCreditLimit(Request $req){
        $req->validate([
            'credit_limit'  => ['required', 'integer'],
            'user_id'       => ['required', 'string', 'max:100']
        ]);
        
        $user = User::findOrFail(hashids_decode($req->user_id));
        $user->credit_limit = $req->credit_limit;
        $user->save();
        
        return response()->json([
            'success'   => 'User credit updated successfully',
            'reload'    => true
        ]);
    }

}
