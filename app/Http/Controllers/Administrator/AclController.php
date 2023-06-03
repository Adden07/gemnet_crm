<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\AdminAcl;
use DataTables;


class AclController extends Controller
{   

    public function index(Request $req){
        if($req->ajax()){
            $data =                 AdminAcl::with(['admin']);

                                            
            
            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('name', function($data){
                                    return @$data->admin->name;
                                })
                                ->addColumn('ip', function($data){
                                    return @$data->ip;
                                })
                                ->filter(function($query) use ($req){
                                    // if(isset($req->search)){
                                    //     $query->where(function($search_query) use ($req){
                                    //         $search = $req->search;
                                    //         $search_query->orWhere('created_at', 'LIKE', "%$search%")
                                    //                     ->orWhere('type', 'LIKE', "%$search%")
                                    //                     ->orWhere('amount', 'LIKE', "%$search%")
                                    //                     ->orWhere('old_balance', 'LIKE', "%$search%")
                                    //                     ->orWhere('new_balance', 'LIKE', "%$search%")
                                    //                     ->orWhereHas('admin',function($q) use ($search){
                                    //                         $q->whereLike(['name','username'], '%'.$search.'%');

                                    //                     });      
                                    //     });
                                    // }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                // ->rawColumns(['date', 'reciever_name', 'added_by', 'type'])
                                ->make(true);
        }
    }

    public function store(Request $req){
        
        $rules =[
            'admin_id'  => ['required'],
            'ip'        => ['required', 'ipv4']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        if(AdminAcl::where('admin_id',hashids_decode($req->admin_id))->where('ip',$req->ip)->doesntExist()){
            if(isset($req->acl_id) && !empty($req->acl_id)){
                $acl = AdminAcl::findOrFail(hashids_decode($req->acl_id));
                $msg = 'Admin ACL Updated Successfully';
            }else{
                $acl = new AdminACl;
                $msg = 'Admin ACL Added Successfully';
            }
    
            $acl->admin_id = hashids_decode($req->admin_id);
            $acl->ip       = $req->ip;
            $acl->save();
    
            return response()->json([
                'success'   => $msg,
                'reload'    => TRUE
            ]);
        }else{
            return response()->json([
                'error'   => 'This IP address already assigned to user',
            ]);
        }


    }
}
