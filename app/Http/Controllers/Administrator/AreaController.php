<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\City;
use App\Models\Area;
use App\Models\Setting;
use App\Models\User;
use DB;
class AreaController extends Controller
{
    public function index(){
        $data = array(
            'title' => 'Areas & Subareas',
            'cities'    => City::get(),
            'areas'     => Area::with(['city','subAreas'])->get(),
        );
        return view('admin.area.index')->with($data);
    }

    public function store(Request $req){

        if((\CommonHelpers::rights('enabled-settings-locations','add-settings-locations'))){
            return redirect()->route('admin.home');
        }


        $rules = [
            'city_id'   => ['required'],
            'area_name' => ['required', 'string', 'max:1000']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        //if area_edit_id is set then update otherwise insert new record
        if(isset($req->area_edit_id) && !empty($req->area_edit_id)){
            $area = Area::findOrFail(hashids_decode($req->area_edit_id));
            $msg  = 'Area Updated Successfully';
            $activity = "updated area-($req->area_name)";
        }else{
            $area = new Area;
            $area->admin_id = auth()->user()->id;
            $msg = 'Area Added Successfully';
            $activity = "added area-($req->area_name)";
        }

        $area->city_id   = hashids_decode($req->city_id);
        $area->area_id   = (!empty($req->area_id)) ? hashids_decode($req->area_id) : 0;
        $area->area_name = $req->area_name;
        $area->type      = (!empty($req->area_id)) ? 'sub_area' : 'area';
        $area->save();

        \CommonHelpers::activity_logs($activity);
        
        return response()->json([
            'success'       => $msg,
            'redirect'      => route('admin.areas.index'),
        ]);
    }

    //get the area list of specified city id
    public function areaList($id){
        if(isset($id) && !empty($id)){
            $areas = Area::where('city_id',hashids_decode($id))->where('area_id',0)->get();    
            $html = view('admin.area.area_list',compact('areas'))->render();

            return response()->json([
                'html'  => $html,
            ]);
        }
    }
    
    //edit area
    public function edit($id){

        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data = array(
                'title' => 'Edit Areas & Subareas',
                'cities'            => City::get(),
                'areas'             => Area::with(['city'])->where('type','area')->get(),
                'edit_area'         => Area::findOrFail(hashids_decode($id)),
                'is_update_area'    => TRUE,
                'subareas'          => Area::with(['city','area'])->where('type','sub_area')->latest()->get(),

            );

            return view('admin.setting.index')->with($data);
        }   
        abort(404);
    }

    //delete area
    public function delete($id){

        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            //if city is not in use of user then delete otherwise not  
            if(User::where('area_id',hashids_decode($id))->doesntExist()){

                DB::transaction(function() use ($id){
                    Area::where('area_id',hashids_decode($id))->delete();//first delete sub_areas
                    
                    $area = Area::find(hashids_decode($id));//delete main area
                    $area->delete();

                    \CommonHelpers::activity_logs("deleted area-($area->area_name)");

                });

                return response()->json([
                    'success'   => 'Area Deleted Successfully',
                    'reload'    => true
                ]);
            }
            return response()->json([
                'error' => 'Area Is In Use',
            ]);
        }
        abort(404);
    }

    public function checkUniqueAreaName(Request $req){
        if(isset($req->city_id) && isset($req->area_name)){
            $area = Area::where('city_id',hashids_decode($req->city_id))->where('area_name',$req->area_name)->when(isset($req->edit_area_id),function($query) use ($req){
                $query->where('id', '!=', hashids_decode($req->edit_area_id));
            })->first();
            
            if(empty($area)){
                return "true";
            }
            return "false";
        }
    }


    //subareas fucntion

    
    //check unique sub area name 
    public function checkUniqueSubarea(Request $req){
        if(isset($req->city_id) && isset($req->area_id) && isset($req->subarea_name)){
            $area = Area::where('city_id',hashids_decode($req->city_id))->where('area_id',hashids_decode($req->area_id))->where('area_name',$req->subarea_name)->when(isset($req->edit_subarea_id),function($query) use ($req){
                $query->where('id','!=',hashids_decode($req->edit_subarea_id));
            })->first();
            if(empty($area)){
                return "true";
            }
            return "false";
        }
    }


    //store subarea
    public function storeSubarea(Request $req){

        if((\CommonHelpers::rights('enabled-settings-locations','add-settings-locations'))){
            return redirect()->route('admin.home');
        }

        $rules = [
            'city_id'       => ['required'],
            'area_id'       => ['required'],
            'subarea_name'  => ['required']
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()]; 
        }

        $validated = $validator->validated();

        if(isset($req->edit_subarea_id) && !empty($req->edit_subarea_id)){
            $subarea = Area::findOrFail(hashids_decode($req->edit_subarea_id));
            $msg = 'Subarea Updated Successfully';
            $activity = "updated subarea-($subarea->area_name-$req->subarea_name)";
        }else{
            $subarea = new Area;
            $subarea->admin_id = auth()->user()->id;
            $msg = 'Subarea Added Successfully';
            $activity = "added subarea-($subarea->area_name-$req->subarea_name)";
        }

        $subarea->city_id    = hashids_decode($validated['city_id']);
        $subarea->area_id    = hashids_decode($validated['area_id']);
        $subarea->area_name  = $validated['subarea_name'];
        $subarea->type       = 'sub_area';
        $subarea->save();

        \CommonHelpers::activity_logs($activity);

        return response()->json([
            'success'   => $msg,
            'redirect'  => route('admin.settings.index')    
        ]);

    }

    //edit area
    public function editSubarea($id){

        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }


        if(isset($id) && !empty($id)){
            $data = array(
                'title'                 => 'Edit Areas & Subareas',
                'cities'                => City::get(),
                'areas'                 => Area::with(['city'])->where('type','area')->get(),
                'edit_subarea'          => Area::findOrFail(hashids_decode($id)),
                'subareas'              => Area::with(['city','area'])->where('type','sub_area')->latest()->get(),
                'is_update_subarea'     => TRUE,
                'edit_setting'          => Setting::where('admin_id',auth()->user()->id)->first(),
            );
            
            $data['areas_list'] = Area::where('city_id',$data['edit_subarea']['city_id'])->where('type','area')->get();

            // \CommonHelpers::activity_logs('edit-area');

            return view('admin.setting.index')->with($data);
        }   
        abort(404);
    }

    //delete subarea
    public function deleteSubarea($id){

        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }
        
        if(isset($id) && !empty($id)){
            //if city is not in use of user then delete otherwise not  
            if(User::where('subarea_id',hashids_decode($id))->doesntExist()){
                
                $sub_area = Area::find(hashids_decode($id));
                $sub_area->delete();
                
                \CommonHelpers::activity_logs("deleted-($sub_area->area_name)");

                return response()->json([
                    'success'   => 'Subarea Deleted Successfully',
                    'redirect'     => route('admin.settings.index'),
                ]);
            }
            return response()->json([
                'error' => 'Subarea Is In Use',
            ]);
        }
        abort(404);
    }

    //subareas list
    public function subareaList($id){
        if(isset($id) && !empty($id)){
            if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'superadmin'){
                $subareas = Area::where('area_id',hashids_decode($id))->where('type','sub_area')->get();
            }else{
                $subareas = auth()->user()->areas()->where('type','sub_area')->get()->where('area_id',hashids_decode($id));
            }
            $html = view('admin.user.subarea_list',compact('subareas'))->render();
            // dd(hashids_decode($id));
            return response()->json([
                'html'  => $html,
            ]);
        }
        abort(404);
    }
}
