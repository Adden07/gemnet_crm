<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\City;
use App\Models\Area;
use App\Models\User;
use App\Models\AdminArea;
use DB;

class CityController extends Controller
{
    //store and update the city
    public function store(Request $req){
        
        if((\CommonHelpers::rights('enabled-settings-locations','add-settings-locations'))){
            return redirect()->route('admin.home');
        }

        $rules = [
            'city_name' => ['required', Rule::unique('cities')->ignore(@hashids_decode($req->city_id))]
        ];

        $validator = Validator::make($req->all(), $rules)->setAttributeNames(['name'=>'city_name']);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $validated = $validator->validated();

        if(isset($req->city_id)){   
            $city     = City::findOrFail(hashids_decode($req->city_id));
            $msg      = 'City Updated Successfully';
            $activity =  "edited city-($city->city_name-$req->city_name)";
        }else{
            $city            = new City;
            $city->admin_id  = auth()->user()->id;
            $msg             = 'City Added Successfully';
            $activity        =  "added city-$req->city_name";        }

        $city->city_name = $validated['city_name'];
        $city->save();

        \CommonHelpers::activity_logs($activity);

        return response()->json([
            'success'      => $msg,
            'redirect'     => route('admin.settings.index'),
        ]);

    }
    
    //edit city
    public function edit($id){
        
        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            $data = array(
                'title'            => 'Edit City',
                'cities'           => City::get(),
                'areas'            => Area::with(['city','area'])->get(),
                'subareas'         => Area::with(['city','area'])->where('type','sub_area')->latest()->get(),
                'edit_city'        => City::findOrFail(hashids_decode($id)),
                'is_update_city'   => TRUE,
            );
            // \CommonHelpers::activity_logs('edit-city');

            return view('admin.setting.index')->with($data);
        }
        abort(404);
    }

    //delete city
    public function delete($id){
        
        if((\CommonHelpers::rights('enabled-settings-locations','edit-settings-locations'))){
            return redirect()->route('admin.home');
        }

        if(isset($id) && !empty($id)){
            //if city is not in use of user then delete otherwise not  
            if(User::where('city_id',hashids_decode($id))->doesntExist()){
                $areas_subareas = Area::where('city_id',hashids_decode($id))->get()->pluck('id')->toArray();

               DB::transaction(function() use ($areas_subareas,$id){
                    
                Area::destroy($areas_subareas);
                    AdminArea::destroy('area_id');
                    
                    $city = City::find(hashids_decode($id));
                    $city->delete();
               });
               
               \CommonHelpers::activity_logs("delete city $city->city_name");

                return response()->json([
                    'success'   => 'City Deleted Successfully',
                    'redirect'     => route('admin.settings.index'),
                ]);
            }
            return response()->json([
                'error' => 'City Is In Use',
            ]);
        }
    }

    //check city name is unique or not
    public function checkUniqueName(Request $req){
        if(isset($req->city_name) && !empty($req->city_name)){
            
            $city = City::where('city_name',$req->city_name)->when(isset($req->id),function($query) use ($req){
                $query->where('id', '!=', hashids_decode($req->id));
            })->first();

            if(empty($city)){
                return "true";
            }else{
                return "false";

            } 
        }
    }
}
