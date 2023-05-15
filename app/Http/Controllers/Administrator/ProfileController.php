<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\ActivityLog;
use App\Models\FranchisePackage;
use App\Models\Payment;
use App\Models\User;
use App\Models\LoginFailLog;
use DataTables;
use App\Models\UserRolePermission;

class ProfileController extends Controller
{
    public function index(Request $req){

        $data = array(
            'title'                 => 'Profile',
            'user_details'          => Admin::where('id',auth()->user()->id)->first(),
            'activity_logs'         => ActivityLog::where('user_id',auth()->user()->id)->latest()->paginate(100),
            'packages'              => NULL,
            'login_fails'           => LoginFailLog::where('username',auth()->user()->username)->get(),
        );
        
        // $packages      = FranchisePackage::with(['admin'])->where('added_to_id',auth()->user()->id)->orderBY('cost','ASC')->get();
        // $childIds      = CommonHelpers::getChildIds(); //get child ids
        // $data['users'] = User::select(['admin_id', 'package'])->whereIn('admin_id',$childIds)->get();
        // if(auth()->user()->user_type == 'franchise'){//if user if franchise 
    
        //     $data['packages'] = $packages->where('status','active');
            
        // }elseif(auth()->user()->user_type == 'dealer'){//if user is dealer then get only those pacakges which are active in franchise
            
        //     $ids = DealerController::getParentActivePacakges($packages);
        //     $data['packages'] = $packages->whereIn('package_id',$ids)->where('status','active');
        
        // }elseif(auth()->user()->user_type == 'sub_dealer'){//if user is subealer then get those packagese which are active in franchise and dealer
        //     $ids = SubDealerController::getParentActivePacakges($packages);
        //     $data['packages'] = $packages->whereNotIn('package_id',$ids)->where('status','active');
        // }
        if($req->ajax()){
            $data =  Payment::with(['admin'])->where('receiver_id',auth()->user()->id);
                                    
            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('date',function($data){
                                    $date = '';
                                    if(date('l',strtotime($data->created_at)) == 'Saturday')
                                        $date = "<span class='badge' style='background-color: #0071bd'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Sunday')
                                        $date = "<span class='badge' style='background-color: #f3872f'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Monday') 
                                        $date = "<span class='badge' style='background-color: #236e96'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Tuesday')
                                        $date = "<span class='badge' style='background-color: #ef5a54'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Wednesday')
                                        $date = "<span class='badge' style='background-color: #8b4f85'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Thursday')
                                        $date = "<span class='badge' style='background-color: #ca4236'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";
                                    elseif(date('l',strtotime($data->created_at)) == 'Friday')
                                        $date = "<span class='badge' style='background-color: #6867ab'>".date('d-M-Y H:i A',strtotime($data->created_at))."</span>";

                                    return $date;
                                })
                                ->addColumn('added_by',function($data){
                                    $added_by = '';
                                    if(@$data->admin->id == 10)
                                        $added_by = "<span class='badge badge-danger'>".$data->admin->name."</span>";
                                    else 
                                        $added_by =  @$data->admin->name."(<strong>".@$data->admin->username."</strong>)";
                                    
                                    return $added_by;
                                })
                                ->addColumn('type',function($data){
                                    $type = '';
                                    if($data->type == 0)
                                        $type = "<span class='badge badge-danger'>System</span>";
                                    else   
                                        $type = "<span class='badge badge-success'>Person</span>";
                                    
                                    return $type;
                                })
                                ->addColumn('amount',function($data){
                                    return number_format($data->amount);
                                })
                                ->addColumn('old_balance',function($data){
                                    return number_format($data->old_balance);
                                })
                                ->addColumn('new_balance',function($data){
                                    return number_format($data->new_balance);
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->username)){
                                        $query->where('receiver_id',hashids_decode($req->username));
                                    }
                                    if(isset($req->added_by)){
                                        if($req->added_by == 'system'){
                                            $query->where('type',0);
                                        }elseif($req->added_by == 'person'){
                                            $query->where('type',1);
                                        }
                                    }
                                    if(isset($req->from_date) && isset($req->to_date)){
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);
                                    }
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->orWhere('created_at', 'LIKE', "%$search%")
                                                        ->orWhere('type', 'LIKE', "%$search%")
                                                        ->orWhere('amount', 'LIKE', "%$search%")
                                                        ->orWhere('old_balance', 'LIKE', "%$search%")
                                                        ->orWhere('new_balance', 'LIKE', "%$search%")
                                                        ->orWhereHas('admin',function($q) use ($search){
                                                            $q->whereLike(['name','username'], '%'.$search.'%');

                                                        });      
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                ->rawColumns(['date', 'reciever_name', 'added_by', 'type'])
                                ->make(true);
        }
        // dd('dones');
        return view('admin.profile.index')->with($data);
    }

    public function updatePassword(Request $req){
        
        $rules = [
            'password'  => ['required', 'min:6', 'confirmed']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $validated = $validator->validated();

        Admin::where('id',auth()->user()->id)->update([
            'password'  => Hash::make($validated['password'])
        ]);

        return response()->json([
            'success'   => 'Password Updated Successfully',
            'reload'    => TRUE
        ]);
    }

    //disable user like admin,and whole franchise network
    public function disableUser($id){
        if(isset($id) && !empty($id)){
            
            $user_type = auth()->user()->user_type;
            $status    = 'disabled';
            
            if($user_type == 'superadmin'){
                $status = 'disabled';
            }elseif($user_type == 'admin'){
                $status = 'disabled_by_admin';
            }
            $user = Admin::findOrFail(hashids_decode($id));
            $user->is_active = $status;
            $user->save();

            return response()->json([
                'success'   => 'User Disabled Successfully',
                'reload'    => TRUE
            ]);
        }
        abort(404);
    }

    public function EnableUser($id){
        if(isset($id) && !empty($id)){
            $user = Admin::findOrFail(hashids_decode($id));
            $user->is_active = 'active';
            $user->save();

            return response()->json([
                'success'  => 'User Enabled Successfully',
                'reload'    => TRUE
            ]); 
        }
    }

        //update credit limit
    public function updateCreditLimit(Request $req){
        
        // if(\CommonHelpers::rights('enabled-franchise','view-franchise')){
        //     return redirect()->route('admin.home');
        // }
        if(isset($req->user_id) && !empty($req->user_id)){
            $rules = [
                'credit_limit'      => ['required', 'numeric', 'integer', 'digits_between:1,7'],
            ];
        
            $validator = Validator::make($req->all(),$rules);

            if($validator->fails()){
                return ['errors'    => $validator->errors()];
            }
                            
            $admin       = Admin::findOrFail(hashids_decode($req->user_id));
            $activity   = 'updated-credit-limit';
        
            $admin->credit_limit = $req->credit_limit;
            $admin->save();

            CommonHelpers::activity_logs($activity);

            return response()->json([
                'success'   => 'Credit Limit Updated Successfully',
                // 'redirect'    => route('admin.franchises.list'),
                'reload'     => TRUE
            ]);
        }
        abort(404);
    }


    public function setPermissionLimit($id, $limit){//set the frnachise network permissions to limited
        if($limit == 0 || $limit == 1){
            $user = Admin::findOrFail(hashids_decode($id));
            $role = UserRolePermission::when($limit == 0, function($query) use ($user){
                $query->where('role_name', $user->user_type);//if limit is 0 then get the get the permissiona according to the user type
            }, function($query) use ($user){//if limit is 1 then get the limtied permission 
                $query->where('role_name', 'limited');
            })->firstOrFail();
            
            $user->limited = $limit;
            $user->user_permissions = $role->permissions;
            $user->save();

            return response()->json([
                'success'   => 'User limit has been updated successfully',
                'reload'    => true
            ]);
        }
        abort(404);
    }
}
