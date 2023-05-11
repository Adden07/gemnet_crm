<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ledger;
use App\Models\Admin;
use App\Models\Payment;
use App\Models\User;
use DB;
use DataTables;
use Exception;

class PaymentController extends Controller
{
    public function index(Request $req){
        
        if(CommonHelpers::rights('enabled-finance','enabled-payments')){
            return redirect()->route('admin.home');
        }
        $admin_ids = Admin::where('user_type','admin')->get()->pluck('id')->toArray();
        
        if($req->ajax()){
            $data =                 Payment::with(['admin', 'receiver'])
                                            ->select('payments.*');

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
                                    return $data->receiver->username;
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
                                ->addColumn('action', function($data){
                                $html = "<button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.accounts.payments.delete', ['id'=>$data->hashid])." class='btn btn-danger btn-xs waves-effect waves-light'>
                                        <span class='btn-label'><i class='icon-trash></i>
                                        </span>Delete
                                    </button>";
                                return $html;
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
                                ->rawColumns(['date', 'reciever_name', 'added_by', 'type', 'action'])
                                ->make(true);
                                // ->with('total_amount',function() use ($data){
                                //     return $data->sum('amount');
                                // });
        }
        $data = array(
            'title' => 'Payments',
            'admins'        => Admin::where('user_type','!=','superadmin')->get(),
        );
        return view('admin.payment.all_payments')->with($data);
    }

    public function add(Request $req){

        if(CommonHelpers::rights('enabled-finance','add-payments')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title'     => 'Add Payment',
            'user_type' => auth()->user()->user_type,
            'users'     => User::latest()->get(),
        );

        return view('admin.payment.add_payment')->with($data);
    }

    //update and store payment
    public function store(Request $req){
        
        $rules = [
            'type'              => ['required', 'in:cash,online'],
            'receiver_id'       => ['required','string', 'max:100'],
            'amount'            => ['required', 'integer', 'min:1'],
            'transaction_id'    => [Rule::requiredIf($req->type == 'online'), 'nullable', 'string'],
            'transaction_image' => [Rule::requiredIf($req->type == 'online'), 'nullable', 'mimes:jpg,jpeg,png', 'max:2000'],
            'payment_id'        => ['nullable', 'string', 'max:100']
        ];
        
        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }
        $msg = '';

        DB::transaction(function() use (&$req, &$msg){
            
            if($req->hasFile('transaction_image')){
                
                $req->transaction_image  = CommonHelpers::uploadSingleFile($req->transaction_image, 'admin_uploads/transactions/', "png,jpeg,jpg", 2000);
            }
        
            $user = User::findOrFail(hashids_decode($req->receiver_id));//get the user
            
            if(isset($req->payment_id) && !empty($req->payment_id)){
                $msg = [
                    'success'   => 'Transaction added successfully',
                    'redirect'    => route('admin.accounts.payments.index')
                ];
            }else{
                Payment::create($this->createPaymentArr($req, $user));
                $msg = [
                    'success'   => 'Transaction added successfully',
                    'redirect'    => route('admin.accounts.payments.index')
                ];
            }
            $user->increment('user_current_balance', $req->amount);
            $user->save();

        });

        // CommonHelpers::activity_logs("added payment-(".$GLOBALS['username'].")");

        return response()->json($msg);

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
            
            // CommonHelpers::activity_logs('edit-payment');

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

    public function createPaymentArr(object $arr, object $user_arr){
        return [    
            'admin_id'  => auth()->id(),
            'transaction_id'    => $arr->transaciton_id,
            'receiver_id'       => $user_arr->id,
            'amount'            => (int) $arr->amount,
            'new_balance'       => (int) $user_arr->user_current_balance + $arr->amount,
            'old_balance'       => (int) $user_arr->user_current_balance,
            'type'              => $arr->type,
            'transaction_image' => $arr->transaction_image,
            'created_at'        => date('y-m-d H:i:s')
        ];
    }

    public function delete($id){
        $msg = '';
        try{
            DB::transaction(function() use (&$id, &$msg){
                $payment = Payment::findOrFail(hashids_decode($id));
                User::findOrFail($payment->receiver->id)->decrement('user_current_balance', $payment->amount);
                $payment->delete();
            });
            $msg = [
                'success'   => 'Transaction deleted successfully',
                'reload'    => true
            ];
        }catch(Exception $e){
            $msg = [
                'error' => 'Some errors occured transaciton could not be deleted',
            ];            
        }
        return response()->json($msg);
    }

    public function approvePayments(Request $req){
        
        if(CommonHelpers::rights('enabled-finance','enabled-payments')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
            $data =                 Payment::with(['admin', 'receiver'])
                                            ->select('payments.*');

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
                                ->addColumn('approved_date',function($data){
                                    if($data->approved_date != null){
                                        return date('d-M-Y H:i:s', strtotime($data->approved_date));
                                    }
                                    return '';
                                })
                                ->addColumn('reciever_name',function($data){
                                    return $data->receiver->username;
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
                                ->addColumn('status', function($data){
                                        if($data->status == 0){
                                            $html = '<span class="badge badge-danger">Pending</span>';
                                        }else{
                                            $html = '<span class="badge badge-success">Approved</span>';
                                        }
                                        return $html;
                                    })
                                ->addColumn('action', function($data){
                                    $html = '';
                                    if($data->approved_by_id == null){
                                        $html = "<button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.accounts.payments.approve_payment', ['id'=>$data->hashid])." class='btn btn-danger btn-xs waves-effect waves-light'><span class='btn-label'>
                                    <i class='icon-trash></i>
                                        </span>Delete
                                    </button>";
                                    }
                                return $html;
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                ->rawColumns(['date', 'approved_date', 'reciever_name', 'added_by', 'type', 'status', 'action'])
                                ->make(true);
        }
        $data = array(
            'title'         => 'Approve payments',
        );
        return view('admin.payment.approve_payments')->with($data);
    }

    public function approvePayment($id){
        Payment::where('id', hashids_decode($id))->update(['status'=>1, 'approved_by_id'=>auth()->id(), 'approved_date'=>date('Y-m-d H:i:s')]);
        return response()->json([
            'success'   => 'Payment approved successfully',
            'reload'    => true 
        ]);
    }
}
