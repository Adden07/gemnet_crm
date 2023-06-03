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
                                    $name =  @$data->admin->name;
                                    return $name;
                                })
                                ->addColumn('username', function($data){
                                    return @$data->admin->username;
                                })
                                ->addColumn('ip', function($data){
                                    return @$data->ip;
                                })
                                ->addColumn('action', function($data){
                                    $html = "<a href='".route('admin.settings.edit_acl',['id'=>$data->hashid])."' class='btn btn-warning btn-xs waves-effect waves-light'>
                                    <span class='btn-label'><i class='icon-pencil'></i></span>Edit
                                </a>";
                                $html .= "<button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.settings.delete_acl', ['id'=>$data->hashid])." class='btn btn-danger btn-xs waves-effect waves-light'><span class='btn-label'>
                                    <i class='icon-trash'></i>
                                        </span>Ddelete
                                    </button>";
                                return $html;
                                })
                                ->filter(function($query) use ($req){
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                ->rawColumns(['name', 'action'])
                                ->make(true);
        }
    }

    public function store(Request $req){
        
        $rules =[
            'admin_id'      => ['required'],
            'ip'            => ['required', 'ipv4'],
            'admin_acl_id'  => ['nullable', 'string', 'max:100']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        $check_ip = AdminAcl::where('admin_id',hashids_decode($req->admin_id))->where('ip',$req->ip)
                            ->when(isset($req->admin_acl_id), function  ($query) use ($req){
                                $query->where('id', '!=', hashids_decode($req->admin_acl_id));
                            })->doesntExist();
        if($check_ip){
            if(isset($req->admin_acl_id) && !empty($req->admin_acl_id)){
                $acl = AdminAcl::findOrFail(hashids_decode($req->admin_acl_id));
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
                'redirect'  => route('admin.settings.index')
            ]);
        }else{
            return response()->json([
                'error'   => 'This IP address already assigned to user',
            ]);
        }


    }
}
