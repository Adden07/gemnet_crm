<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\CommonHelpers;
use App\Models\Transaction;
use App\Models\Admin;
use DataTables;
class TransactionController extends Controller
{   
    public function index(Request $req){
        
        // if(CommonHelpers::rights('enabled-finance','enabled-payments')){
        //     return redirect()->route('admin.home');
        // }        
        if($req->ajax()){
            $data =                 Transaction::
                                                with(['admin', 'user'=>function($query){
                                                    $query->select('users.*');
                                                }])
                                                // ->has('admin')
                                                // ->has('user')
                                                ->when(auth()->user()->user_type != 'admin', function($query){
                                                    $query->where('admin_id', auth()->id());
                                                });
                                            

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
                                    $name = '';
                                    if($data->admin->user_type == 'franchise')
                                        $name = "<span class='badge' style='background-color:#2875F3'>".$data->admin->username."</span>";
                                    elseif($data->admin->user_type == 'dealer')
                                        $name = "<span class='badge' style='background-color:#3ABC01'>".$data->admin->username."</span>";
                                    elseif($data->admin->user_type == 'sub_dealer')
                                        $name = "<span class='badge' style='background-color:#3ABC01'>".$data->admin->username."</span>";

                                    return $name;
                                })
                                ->addColumn('user',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>(isset($data->user->id)) ? hashids_encode($data->user->id) : 1])." target='_blank'>".@$data->user->username."</a>";
                                    
                                    // return @wordwrap($data->user->username,10,"<br>\n");
                                })
                                ->addColumn('type',function($data){
                                    $type = '';
                                    if($data->type == 0)
                                        $type = "<span class='badge badge-danger'>Invoice</span>";
                                    else   
                                        $type = "<span class='badge badge-success'>Payment</span>";
                                    
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
                                ->addColumn('transaction_id',function($data){
                                    return $data->transaction_id;
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

                                    $query->when(isset($req->franchise_id) || $req->dealer_id || $req->subdealer_id, function($query) use ($req){

                                        $id = array();
                                        
                                        if(isset($req->franchise_id)){
                                            $id = [hashids_decode($req->franchise_id)];
                                        }

                                        if(isset($req->dealer_id)){
                                            if($req->dealer_id == 'all_dealers'){
                                                $dealer_ids =    Admin::select('id')
                                                                    ->where('added_to_id',hashids_decode($req->franchise_id))
                                                                    ->get()
                                                                    ->pluck('id')
                                                                    ->toArray();
                                                $id     =   $dealer_ids;
                                            }else{
                                                $id = [hashids_decode($req->dealer_id)];
                                            }
                                        }
                                        if(isset($req->subdealer_id)){
                                            if($req->subdealer_id == 'all_subdealers'){
                                                $subdealer_ids = Admin::select('id')
                                                                ->where('added_to_id',hashids_decode($req->dealer_id))
                                                                ->get()
                                                                ->pluck('id')
                                                                ->toArray();
                                                $id     =   $subdealer_ids;
                                            }else{
                                                $id = [hashids_decode($req->subdealer_id)];
                                            }
                                        }

                                        $query->whereIn('admin_id',$id);

                                    });
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search;
                                            $search_query->orWhere('created_at', 'LIKE', "%$search%")
                                                        ->orWhere('transaction_id', 'LIKE', "%$search%")
                                                        ->orWhere('amount', 'LIKE', "%$search%")
                                                        ->orWhere('old_balance', 'LIKE', "%$search%")
                                                        ->orWhere('new_balance', 'LIKE', "%$search%")
                                                        ->orWhereHas('user',function($q) use ($search){
                                                                $q->whereLike(['name','username'], '%'.$search.'%');

                                                            })
                                                        ->orWhereHas('admin',function($q) use ($search){
                                                            $q->whereLike(['name','username'], '%'.$search.'%');

                                                        });      
                                        });
                                    }
                                    if(isset($req->type) && $req->type != 'all'){
                                        $query->where('type',$req->type);
                                    }
                                    
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                ->rawColumns(['date', 'admin', 'type', 'added_by', 'user'])
                                ->make(true);
        }
        $data = array(
            'title'            => 'Transactions',
            'franchises'       => Admin::where('user_type', 'franchise')->where('is_active', 'active')->get(),
        );
        return view('admin.transaction.all_transactions')->with($data);
    }
}
