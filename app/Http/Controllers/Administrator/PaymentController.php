<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ledger;
use App\Models\Admin;
use App\Models\Payment;
use DB;
use DataTables;
class PaymentController extends Controller
{
    public function index(Request $req){
        
        if(\CommonHelpers::rights('enabled-finance','enabled-payments')){
            return redirect()->route('admin.home');
        }

        $admin_ids = Admin::where('user_type','admin')->get()->pluck('id')->toArray();
        
        if($req->ajax()){
            $data =                 Payment::with(['admin'=>function($query){
                                                // $query->select(\DB::raw('CONCAT(admins.name)'));
                                                $query->select('admins.*');
                                            },'receiver'])
                                            ->select('payments.*')
                                            ->when(auth()->user()->user_type == 'admin',function($query) use ($admin_ids){
                                                $query->whereIn('admin_id',$admin_ids);
                                            },function($query){
                                                $query->where('admin_id',auth()->user()->id);
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
                                ->addColumn('reciever_name',function($data){
                                    $name = '';
                                    if($data->receiver->user_type == 'franchise')
                                        $name = "<span class='badge' style='background-color:#2875F3'>".$data->receiver->name." (".$data->receiver->username.")</span>";
                                    elseif($data->receiver->user_type == 'dealer')
                                        $name = "<span class='badge' style='background-color:#3ABC01'>".$data->receiver->name." (".$data->receiver->username.")</span>";
                                    elseif($data->receiver->user_type == 'sub_dealer')
                                        $name = "<span class='badge' style='background-color:#3ABC01'>".$data->receiver->name." (".$data->receiver->username.")</span>";

                                    return $name;
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
                                                        ->orWhereHas('receiver',function($q) use ($search){
                                                                $q->whereLike(['name','username'], '%'.$search.'%');

                                                            })
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
                                // ->with('total_amount',function() use ($data){
                                //     return $data->sum('amount');
                                // });
        }
        $data = array(
            'title' => 'Payments',
            // 'transactions'  => Payment::with(['admin','receiver'])
            //                                 ->when(isset($req->username),function($query) use ($req){
            //                                     $query->where('receiver_id',hashids_decode($req->username));
            //                                 })
            //                                 ->when(isset($req->from_date) && isset($req->to_date),function($query) use ($req){
            //                                     $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);
            //                                 })
            //                                 ->when(isset($req->added_by), function($query) use ($req){
            //                                     if($req->added_by == 'system'){
            //                                         $query->where('type',0);
            //                                     }elseif($req->added_by == 'person'){
            //                                         $query->where('type',1);
            //                                     }
            //                                 })
            //                                 ->when(auth()->user()->user_type == 'admin',function($query) use ($admin_ids){
            //                                     $query->whereIn('admin_id',$admin_ids);
            //                                 },function($query){
            //                                     $query->where('admin_id',auth()->user()->id);
            //                                 })
            //                                 ->latest()
            //                                 // ->dd(),
            //                                 ->paginate(50)
            //                                 ->withQueryString(),
            'admins'        => Admin::where('user_type','!=','superadmin')->get(),
        );
        return view('admin.payment.all_payments')->with($data);
    }

    public function add(Request $req){

        if(\CommonHelpers::rights('enabled-finance','add-payments')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title'     => 'Add Payment',
            'user_type' => auth()->user()->user_type,
        );

        if(auth()->user()->user_type == 'admin'){//if user is admin display franchise
            $data['franchises']    = Admin::where('user_type','franchise')->latest()->where('is_active','active')->get();
        }elseif(auth()->user()->user_type == 'franchise'){//is user is franchise only display dealer
            $data['dealers']       = Admin::where('added_to_id',auth()->user()->id)->where('user_type','dealer')->where('is_active','active')->get();
        }elseif(auth()->user()->user_type == 'dealer'){//if user is dealer only display subdealers
            $data['subdealers']    = Admin::where('added_to_id',auth()->user()->id)->where('user_type','sub_dealer')->where('is_active','active')->get();
        }

        // \CommonHelpers::activity_logs('add-payment');

        return view('admin.payment.add_payment')->with($data);
    }

    //update and store payment
    public function store(Request $req){
        
        $rules = [
            'type'              => ['required', 'in:franchise,dealer,subdealer'],
            'user_type'         => ['required', 'in:admin,franchise,dealer,sub_dealer'],
            'franchise_id'      => [Rule::requiredIf($req->user_type == 'admin')],
            'dealer_id'         => [Rule::requiredIf($req->user_type == 'franchise')],
            'subdealer_id'      => [Rule::requiredIf($req->user_type == 'dealer')],
        ];

        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        // $msg         = 'Payment Added Successfully';
        $transaction_id = rand(1111111111,9999999999);

        DB::transaction(function() use ($req, $transaction_id){
            #0 means system 1 means person
            $username = '';
            if(auth()->user()->user_type == 'admin'){//if user is admin then add transaction itself too
                if($req->type == 'franchise' && isset($req->franchise_id)){//is type is franchise add transaction only in franchise
                    $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->franchise_id, $req->amount, $transaction_id,1,);
                }elseif($req->type == 'dealer' && isset($req->dealer_id)){//if type is dealer add transaciton in dealer and franchise
                    $this->makeTransaction(auth()->user()->id, $req->franchise_id, $req->amount, $transaction_id,0,);
                    $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->dealer_id, $req->amount, $transaction_id,1,);
                }elseif($req->type == 'subdealer' && isset($req->subdealer_id)){//if type is subdealer add transction in subdealer,dealer,franchise
                    $this->makeTransaction(auth()->user()->id, $req->franchise_id, $req->amount, $transaction_id,0,);
                    $this->makeTransaction(auth()->user()->id, $req->dealer_id, $req->amount, $transaction_id,0,);
                    $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->subdealer_id, $req->amount, $transaction_id,1,);
                }
            }elseif(auth()->user()->user_type == 'franchise'){//is user is franchsie
                if($req->type == 'dealer' && isset($req->dealer_id)){//if type is dealer add transaction in dealer
                    $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->dealer_id, $req->amount, $transaction_id,1,);
                }elseif($req->type == 'subdealer' && isset($req->subdealer_id)){//is type is subdealer add transaction in subdealer and dealer
                    $this->makeTransaction(auth()->user()->id, $req->dealer_id, $req->amount, $transaction_id,0,);
                    $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->subdealer_id, $req->amount, $transaction_id,1,);
                }
            }elseif(auth()->user()->user_type == 'dealer'){//if user is dealer add transaction only in subdealer
                $GLOBALS['username'] = $this->makeTransaction(auth()->user()->id, $req->subdealer_id, $req->amount, $transaction_id,1,);
            }


        });

        \CommonHelpers::activity_logs("added payment-(".$GLOBALS['username'].")");

        return response()->json([
            'success'   => "Payment Added Successfully",
            'redirect'     => route('admin.accounts.payments.index')
        ]);

    }
    //insert record in transaciton table and also update the receiver balance in admin table
    private function makeTransaction($admin_id,$receiver_id,$amount,$transaction_id,$type=0){
        //get recceiver data
        $receiver = Admin::findOrFail(hashids_decode($receiver_id));
        
        //insert record in transaciton table
        $transaction = new Ledger;
        $transaction->admin_id          = hashids_decode($receiver_id);
        $transaction->transaction_id    = $transaction_id;
        $transaction->user_id           = NULL;
        $transaction->amount            = $amount;
        $transaction->old_balance       = $receiver->balance;
        $transaction->new_balance       = $receiver->balance + $amount;
        $transaction->type              = 1;
        $transaction->created_at        = date('Y-m-d H:i:s');
        $transaction->save();

        //insert record in payments table
        $payment = new Payment;
        $payment->admin_id          = $admin_id;
        $payment->transaction_id    = $transaction_id;
        $payment->receiver_id           = hashids_decode($receiver_id);
        $payment->amount            = $amount;
        $payment->old_balance       = $receiver->balance;
        $payment->new_balance       = $receiver->balance + $amount;
        $payment->type              = $type;
        $payment->created_at        = date('Y-m-d H:i:s');
        $payment->save();

        //update receier balance
        $receiver = Admin::findOrFail(hashids_decode($receiver_id));
        $receiver->increment('balance',$amount);
        $receiver->save();

        return $receiver->username;
    }

    //edit payment
    public function edit($id){
        if(isset($id) && !empty($id)){
            $data = array(
                'title' => 'Edit Payment',
                'edit_transaction'  => Ledger::findOrFail(hashids_decode($id)),
                'franchises'        => Admin::where('user_type','franchise')->latest()->get(),
                'is_update'         => TRUE
            );
            
            // \CommonHelpers::activity_logs('edit-payment');

            return view('admin.payment.add_payment')->with($data);
        }
    }

    //get user balance 
    public function getBalance($id){
        $user = Admin::findOrFail(hashids_decode($id));
        return response()->json([
            'balance'   => $user->balance,
        ]);
    }
}
