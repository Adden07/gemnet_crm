<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $req){
        
        if(\CommonHelpers::rights(true,'view-activity-log')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
            $data = ActivityLog::with('user')
                            // ->when(auth()->user()->user_type == 'sales_person' || auth()->user()->user_type == 'filed_engineer',function($query){
                            //     $query->where('user-id', auth()->id());
                            // })
                            ->when(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'supervisor', function($query){
                                $query->where('user_id', '!=', 0);
                            }, function($query){
                                if(auth()->user()->user_type != 'superadmin'){
                                    $query->where('user_id', auth()->id());
                                }
                            });

            return DataTables::of($data)
                                     ->addIndexColumn()
                                     ->addColumn('username',function($query){
                                        return @$query->user->username;
                                     })
                                     ->addColumn('type',function($query){
                                        return @$query->user->user_type;
                                     })
                                     ->addColumn('ip',function($query){
                                        return $query->user_ip;
                                     })
                                     ->addColumn('activity',function($query){
                                        return $query->activity;
                                     })
                                     ->addColumn('date',function($query){
                                        return $query->created_at;
                                     })
                                     ->orderColumn('DT_RowIndex', function($query, $o){
                                        $query->orderBy('created_at', $o);
                                    })
                                    ->filter(function($query) use ($req){
                                        if(isset($req->from_date) && isset($req->to_date)){
                                            $query->whereDate('created_at', '>=', date('Y-m-d',strtotime($req->from_date)))
                                            ->whereDate('created_at', '<=', date('Y-m-d',strtotime($req->to_date)));
                                            // $query->whereBetween('created_at',[date('Y-m-d',strtotime($req->from_date)),date('Y-m-d',strtotime($req->to_date))]);
                                        }
                                        if(isset($req->search)){
                                            $query->where(function($search_query) use ($req){
                                                $search = $req->search;
                                                $search_query->whereLike([
                                                            // 'username',
                                                            'user_ip',
                                                            'activity',
                                                            'created_at'
                                                        ], 
                                                $search);
                                                // ->orWhereHas('admin', function($q) use ($search) {
                                                //     $q->whereLike(['name','username'], '%'.$search.'%');
                                                // });
                                            });
                                        }
                                    })
                                     ->make(true);
        }
        $data = array(
            'title'         => 'Activity Logs',
            // 'activity_logs' => ActivityLog::with('user')->when(auth()->user()->user_type != 'admin' && auth()->user()->user_type != 'superadmin',function($query){
            //                         $query->where('user_id',auth()->user()->id);
            //                     })->when(auth()->user()->user_type == 'admin',function($query){
            //                         $query->where('user_id','!=',45);
            //                     })->when(isset($req->from_date), function($query) use ($req){
            //                         $query->whereBetween('created_at',[date('Y-m-d',strtotime($req->from_date)),date('Y-m-d',strtotime($req->to_date))]);
            //                     }, function($query){
            //                         $query->whereDate('created_at',now()->today())->orderBy('created_at','DESC');
            //                     })->paginate(50),
        );
        // \CommonHelpers::activity_logs('activity-logs');

        return view('admin.activity_log.all_activity_logs')->with($data);
    }

    // public function search(Request $req){

    //     $data = array(
    //         'title'         => 'Activity Logs',
    //         'activity_logs' => ActivityLog::with('user')->whereBetween('created_at',[date('Y-m-d',strtotime($req->from_date)),date('Y-m-d',strtotime($req->to_date))])->paginate(10)->withQueryString(),
    //     );

    //     // \CommonHelpers::activity_logs('search-logs');

    //     return view('admin.activity_log.all_activity_logs')->with($data);

    // }
}
