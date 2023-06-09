<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Ledger;
use App\Models\Admin;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use DB;
use DataTables;
use Exception;
use Pdf;

class PaymentController extends Controller
{
    public function index(Request $req){
        
        if(CommonHelpers::rights('enabled-finance','view-payments')){
            return redirect()->route('admin.home');
        }
        // $admin_ids = Admin::where('user_type','admin')->get()->pluck('id')->toArray();
        
        if($req->ajax()){
        $data =                 Payment::with(['admin', 'receiver'])
                                        ->select('payments.*')
                                        ->when(auth()->user()->user_type == 'sales_person' || auth()->user()->user_type == 'field_engineer',function($query){
                                            if(auth()->user()->user_type == 'sales_person'){
                                                $query->whereIn('sales_id', auth()->id());
                                            }elseif(auth()->user()->user_type == 'fe_id'){
                                                $query->whereIn('fe_id', auth()->id());
                                            }
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
                                    
                                    return "<a href=".route('admin.users.profile',['id'=>hashids_encode(@$data->receiver->id)])." target='_blank'>".@$data->receiver->username."</a>";

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
                                    if($data->type == 'cash')
                                        $type = "<span class='badge badge-success'>Cash</span>";
                                    elseif($data->type == 'challan')
                                        $type = "<span class='badge badge-info'>Tax-Chalan</span>";
                                    elseif($data->type == 'online')   
                                        $type = "<span class='badge badge-primary'>Online</span>";
                                    elseif($data->type == 'cheque')
                                        $type = "<span class='badge badge-warning'>Cheque</span>";

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
                                ->addColumn('image', function($data){
                                    $action = '';
                                    if(file_exists($data->transaction_image)){
                                        $action = "<a href=".asset($data->transaction_image)." class='btn btn-primary btn-xs waves-effect waves-light' title='Edit' target='_blank'>
                                            <i class='icon-eye'></i>
                                        </a>";
                                    }
                                    
                                   return $action;
                                })
                                ->addColumn('action', function($data){
                                     $html = '';

                                if(auth()->user()->can('print-payments') && $data->type == 'cash'){
                                    $html = " <a href=".route('admin.accounts.payments.receipt_pdf', ['id'=>$data->hashid])." class='btn btn-warning btn-xs waves-effect waves-light mr-2' title='print' target='_blank'>
                                                <i class='icon-printer'></i>
                                            </a>";
                                }
                                if(auth()->user()->can('delete-payments')){
                                    $html .= "<button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.accounts.payments.delete', ['id'=>$data->hashid])." class='btn btn-danger btn-xs waves-effect waves-light'>
                                            <span class='btn-label'><i class='icon-trash'></i>
                                            </span>Delete
                                        </button>";
                                }


                                return $html;
                                })
                                ->filter(function($query) use ($req){
                                    if(isset($req->receiver_id) && $req->receiver_id != 'all'){
                                        $query->where('receiver_id',hashids_decode($req->receiver_id));
                                    }
                                    if(isset($req->admin_id) && $req->admin_id != 'all'){
                                        $query->where('admin_id',hashids_decode($req->admin_id));
                                    }
                                    if(isset($req->from_date) && isset($req->to_date)){
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);
                                    }

                                    if(isset($req->type) && $req->type != 'all'){
                                        $query->where('type', $req->type);
                                    }

                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search['value'];
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
                                ->rawColumns(['date', 'reciever_name', 'added_by', 'type', 'action', 'image'])
                                ->make(true);
                                // ->with('total_amount',function() use ($data){
                                //     return $data->sum('amount');
                                // });
        }
        $admin_id = Payment::groupBy('admin_id')->get(['admin_id'])->pluck('admin_id')->toArray();
        $receiver_id = Payment::groupBy('receiver_id')->get(['receiver_id'])->pluck('receiver_id')->toArray();

        $data = array(
            'title' => 'Payments',
            'admins'        => Admin::where('user_type','!=','superadmin')->get(),
            'receivers'     => User::whereIn('id', $receiver_id)->get(['id', 'name', 'username']),
            'admins'        => Admin::whereIn('id', $admin_id)->get(['id', 'name', 'username']), 
            // 'total_payments'=> Payment::sum('amount'),
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
            'users'     => User::when(auth()->user()->user_type == 'sales_person' || auth()->user()->user_type == 'field_engineer',function($query){
                            if(auth()->user()->user_type == 'sales_person'){
                                $query->whereIn('sales_id', auth()->id());
                            }elseif(auth()->user()->user_type == 'fe_id'){
                                $query->whereIn('fe_id', auth()->id());
                            }
                        })->latest()->get(),
        );

        return view('admin.payment.add_payment')->with($data);
    }

    //update and store payment
    public function store(Request $req){
        // dd($req->all());
        $rules = [
            'type'                  => ['required', 'in:cash,online,cheque,challan'],
            'receiver_id'           => ['required','string', 'max:100'],
            'amount'                => ['required', 'integer', 'min:1'],
            // 'transaction_id'        => [Rule::requiredIf($req->type == 'online'), 'nullable', 'string'],
            'online_transaction'    => [Rule::requiredIf($req->type == 'online'), 'nullable', 'string'],
            'online_date'           => [Rule::requiredIf($req->type == 'online'), 'nullable', 'date'],
            'cheque_no'             => [Rule::requiredIf($req->type == 'cheque'), 'nullable', 'integer'],
            'cheque_date'           => [Rule::requiredIf($req->type == 'cheque'), 'nullable', 'date'],
            'transaction_image'     => [Rule::requiredIf($req->type == 'online'), 'nullable', 'mimes:jpg,jpeg,png', 'max:2000'],
            'payment_id'            => ['nullable', 'string', 'max:100'],
            'auto_renew'            => ['required', 'in:1,0'],
            'redirect'              => ['nullable']
        ];
        
        $validator = Validator::make($req->all(),$rules);

        if($validator->fails()){
            return ['errors'    => $validator->errors()];
        }

        $msg  = '';
        $user = User::findOrFail(hashids_decode($req->receiver_id));//get the user

        try{
            DB::transaction(function() use (&$req, &$msg, &$user){
                if($req->hasFile('transaction_image')){ //store image
                    $req->transaction_image  = CommonHelpers::uploadSingleFile($req->transaction_image, 'admin_uploads/transactions/', "png,jpeg,jpg", 2000);
                }
                $transation_arr = $this->transactionArr($user, $req->amount);
                Transaction::insert($transation_arr);
                Payment::create($this->createPaymentArr($req, $user, $transation_arr['transaction_id']));//add payment
                $msg = [//success message
                    'success'   => 'Transaction added successfully',
                    'redirect'    => route('admin.accounts.payments.index')
                ];
                $user->increment('user_current_balance', $req->amount);//update user balance
                $user->save();

                if($req->auto_renew == 1 && $user->status == 'expired'){
                    $package = Package::where('id', $user->c_package)->first();
                    CronController::autoRenew($user->id);
                    // CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_renew', $user->mobile, null,$package->name, null);
                    CommonHelpers::activity_logs("Renew Package - $user->username");//add the activity log 
                }

            });
            CommonHelpers::sendSmsAndSaveLog($user->id, $user->username, 'user_add_payment', $user->mobile, $req->amount, null, $req->type);
            CommonHelpers::activity_logs("Added payment - $user->username");//add the activity log
        }catch(Exception $e){
            $msg = [
                'error' => "Transaction failed some errors occured",
            ];
            CommonHelpers::activity_logs("failed payment - $user->username");//add the activity log
        }
        
        if(isset($req->redirect)){
            $msg = [
                'success'   => 'Payment added successfully',
                'redirect'  => $req->redirect,
            ];
        }

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

    public function createPaymentArr(object $arr, object $user_arr, $transaction_id){
        return [    
            'admin_id'  => auth()->id(),
            'transaction_id'    => $transaction_id,
            'receiver_id'       => $user_arr->id,
            'amount'            => (int) $arr->amount,
            'new_balance'       => (int) $user_arr->user_current_balance + $arr->amount,
            'old_balance'       => (int) $user_arr->user_current_balance,
            'type'              => $arr->type,
            'transaction_image' => $arr->transaction_image,
            'online_transaction'=>$arr->online_transaction,
            'cheque_no'         =>$arr->cheque_no,
            'online_date'       =>$arr->online_date,
            'cheque_date'       =>$arr->cheque_date,
            'created_at'        => date('y-m-d H:i:s')
        ];
    }

    public function delete($id){
        $msg = '';
        try{
            DB::transaction(function() use (&$id, &$msg){
                $payment = Payment::findOrFail(hashids_decode($id));
                $payment->transaciton()->delete();
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
        
        if(CommonHelpers::rights('enabled-finance','view-approve-payments')){
            return redirect()->route('admin.home');
        }
        
        if($req->ajax()){
            $data =                 Payment::with(['admin', 'receiver'])
                                            ->select('payments.*')
                                            ->where('type', 'online');

            return DataTables::of($data)
                                ->addIndexColumn()
                                ->addColumn('checkbox', function ($data) {
                                    $html = '';
                                    if($data->status == 0){
                                        $html = '<input type="checkbox" name="checkbox[]" value="' . $data->hashid . '" onclick="getCheckbox()" class="id_checkbox">';
                                    }
                                    return $html;
                                })
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
                                ->addColumn('online_date',function($data){
                                    if($data->online_date != null){
                                        return date('d-M-Y H:i:s', strtotime($data->online_date));
                                    }
                                    return '';
                                })
                                ->addColumn('reciever_name',function($data){
                                    return "<a href=".route('admin.users.profile',['id'=>hashids_encode($data->receiver->id)])." target='_blank'>{$data->receiver->username}</a>";
                                })
                                ->addColumn('added_by',function($data){
                                    $added_by = '';
                                    if(@$data->admin->id == 10)
                                        $added_by = "<span class='badge badge-danger'>".$data->admin->name."</span>";
                                    else 
                                        $added_by =  @$data->admin->name."(<strong>".@$data->admin->username."</strong>)";
                                    
                                    return $added_by;
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
                                ->addColumn('image', function($data){
                                    $action = '';
                                    if(file_exists($data->transaction_image)){
                                        $action = "<a href=".asset($data->transaction_image)." class='btn btn-primary btn-xs waves-effect waves-light' title='Edit' target='_blank'>
                                            <i class='icon-eye'></i>
                                        </a>";
                                    }
                                    
                                   return $action;
                                })
                                ->addColumn('action', function($data){
                                    $html = '';
                                    if($data->approved_by_id == null){
                                        $html = "<button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.accounts.payments.approve_payment', ['id'=>$data->hashid])." class='btn btn-success btn-xs waves-effect waves-light'><span class='btn-label'>
                                    <i class='icon-check'></i>
                                        </span>Approve
                                    </button>";
                                    }
                                    return $html;
                                })
                                
                                ->filter(function($query) use ($req){
                                    if(isset($req->status) && $req->status != 'all'){
                                        $query->where('status', $req->status);
                                    }
                                    if(isset($req->from_date) && isset($req->to_date)){
                                        // $query->whereBetween('created_at', [$req->from_date, $req->to_date]);
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);

                                    }
                                    
                                    if(isset($req->search)){
                                        $query->where(function($search_query) use ($req){
                                            $search = $req->search['value'];
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
                                    $q->orderBy('created_at', 'asc');
                                })
                                ->rawColumns(['date', 'approved_date', 'reciever_name', 'added_by', 'status', 'action', 'image', 'checkbox'])
                                ->make(true);
        }
        $data = array(
            'title'         => 'Approve payments',
        );
        return view('admin.payment.approve_payments')->with($data);
    }

    public function approvePayment(Request $req){
        if(isset($req->ids)){
            $arr = explode(',', $req->ids[0]);
            $arr = array_map('hashids_decode', $arr);
        }else{
            $arr[] = hashids_decode($req->id);
        }

        Payment::whereIn('id', $arr)->update(['status'=>1, 'approved_by_id'=>auth()->id(), 'approved_date'=>date('Y-m-d H:i:s')]);
        return response()->json([
            'success'   => 'Payment approved successfully',
            'reload'    => true 
        ]);
    }

    public function transactionArr($user, $amount){
        return [
            'transaction_id'    => rand(1111111111,9999999999),
            'admin_id'          => auth()->id(),
            'user_id'           => $user->id,
            'amount'            => $amount,
            'old_balance'       => $user->user_current_balance,
            'new_balance'       => ($user->user_current_balance+$amount),
            'type'              => 1,
            'created_at'        =>now()
        ];
    }

    public function receiptPdf($id){
        // $pdf = PDF::loadView('admin.payment.receipt_pdf', ['data'=>Payment::with(['receiver'])->findOrFail(hashids_decode($id))]);
        // return $pdf->download('receipt.pdf');
        return view('admin.payment.receipt_pdf', ['data'=>Payment::with(['receiver'])->findOrFail(hashids_decode($id))]);
    }
}
