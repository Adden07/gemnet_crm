<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Services\Slug;
class PermissionController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('is_admin');
        
    // }

    public function addType(){
        $data = array(
            'title' => 'Permission Type',
            'permission_types'   => PermissionType::get(),
        );
        return view('admin.permission.permission_type')->with($data);
    }

    public function storeType(Request $request, Slug $slug){

        $rules = [
            'permission_type' => ['required', 'string', 'max:80'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $permission = new PermissionType();
        $permission->slug = $slug->createSlug('permissions', $request->permission_type);
        $msg = [
            'success' => 'Permission has been added',
            'redirect' => route('admin.permissions.add_type'),
        ];

        $permission->name = $request->permission_type;
        $permission->save();

        return response()->json($msg);
    }

    public function addPermission(Request $request, Slug $slug)
    {
        $data = array(
            'title' => 'Permissions',
            'permission_types'  => PermissionType::get(),
            'permissions' => Permission::with(['type'])->orderby('id', 'desc')->get(),
        );
        return view('admin.permission.permission')->with($data);
    }

    public function storePermission(Request $request, Slug $slug)
    {
        $rules = [
            'name' => ['required', 'string', 'max:80'],
            'name'  => ['required', 'string']
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }


        if ($request->permission_id) {
            $permission = Permission::hashidFind($request->permission_id);
            $permission->slug = $slug->createSlug('permissions', $request->name, $permission->id);
            $msg = [
                'success' => 'Permission has been updated',
                'reload' => true,
            ];
        } else {
            $permission = new Permission();
            $permission->slug = $slug->createSlug('permissions', $request->name);
            $msg = [
                'success' => 'Permission has been added',
                'redirect' => route('admin.permissions.add_permission'),
            ];
        }
        $permission->type_id = $request->type_id;
        $permission->name    = $request->name;
        $permission->save();

        return response()->json($msg);
    }

    public function delete(Request $request)
    {
        $permission = Permission::hashidOrFail($request->permission_id);
        $permission->delete();
        return response()->json([
            'success' => 'Permission deleted successfully',
            'remove_tr' => true
        ]);
    }
}
