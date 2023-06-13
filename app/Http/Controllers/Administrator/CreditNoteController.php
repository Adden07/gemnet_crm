<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DataTables;

class CreditNoteController extends Controller
{
    public function index(Request $req){
        if($req->ajax()){
                return DataTables::of(CreditNote::query())
                                    ->addIndexColumn()
                                    ->addColumn('date',function($data){
                                        return date('d-M-Y', strtotime($data->created_at));
                                    })
                                    ->addColumn('reciever_name',function($data){
                                        return "<a href=".route('admin.users.profile',['id'=>hashids_encode(@$data->user->id)])." target='_blank'>".@$data->user->username."</a>";
                                    })
                                    ->addColumn('added_by', function($data){
                                        return $data->admin->username;
                                    })
                                    ->addColumn('amount',function($data){
                                        return number_format($data->amount);
                                    })
                                    ->addColumn('old_balance',function($data){
                                        return number_format(@$data->transaction->old_balance);
                                    })
                                    ->addColumn('new_balance',function($data){
                                        return number_format(@$data->transaction->new_balance);
                                    })
                                    ->orderColumn('DT_RowIndex', function($q, $o){
                                        $q->orderBy('created_at', $o);
                                    })
                                    ->rawColumns(['reciever_name'])
                                    ->make(true);
            }
        $data = array(
            'title' => 'Credit Note',
            'users' => User::latest()->get(),
        );
        return view('admin.credit_note.index')->with($data);
    }

    public function store(Request $req){
        $rules = [
            'user_id'   => ['required', 'string', 'max:100'],
            'invoice_id'=> ['required', 'string', 'max:100'],
            'amount'    => ['required', 'min:1', 'integer'],
            'credit_note_id'=> ['nullable', 'string', 'max:100']
        ];

        $validator = Validator::make($req->all(), $rules);
        
        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }

        $validated = $validator->validated();
        $msg       = null;

        if(isset($validated['credit_note_id']) && !empty($validated['credit_note_id'])){

        }else{
            $user       = User::findOrFail(hashids_decode($validated['user_id']));
            $transaction = new Ledger;
            $credit_note= new CreditNote;
        }
        $transaaction_id =  rand(111111111,999999999);
        $transaction->admin_id          = auth()->id();
        $transaction->transaction_id    = $transaaction_id;
        $transaction->user_id           = $user->id;
        $transaction->amount            = $validated['amount'];
        $transaction->old_balance       = $user->user_current_balance;
        $transaction->new_balance       = $user->user_current_balance + $validated['amount'];
        $transaction->type              = 2;
        $transaction->created_at        = date('Y-m-d H:i:s');
        $transaction->save();
        
        $credit_note->user_id    = hashids_decode($validated['user_id']);
        $credit_note->transaction_id = $transaction->id;
        $credit_note->invoice_id = hashids_decode($validated['invoice_id']);
        $credit_note->amount     = $validated['amount'];
        $credit_note->admin_id     = auth()->id();
        $credit_note->save();

        $user->increment('user_current_balance', $validated['amount']);
        $user->save();

        return response()->json([
            'success'   => "Credit note added successfully",
            'reload'    => true
        ]);
        
    }

    public function getUserInvoices($id){
        $html = '';
        $invoices = Invoice::where('user_id', hashids_decode($id))->get()->map(function($data) use (&$html){
            $html .= "<option value='$data->hashid'>$data->invoice_id</option>";
        });
        return response()->json([
            'html'  => $html,
        ]);
    }

    public function transaction($user_id, $amount, $old_balance){
        $transaction = new Ledger;
        $transaction->admin_id          = auth()->id();
        $transaction->transaction_id    = rand(111111111,999999999);
        $transaction->user_id           = $user_id;
        $transaction->amount            = $amount;
        $transaction->old_balance       = $old_balance;
        $transaction->new_balance       = $old_balance + $amount;
        $transaction->type              = 2;
        $transaction->created_at        = date('Y-m-d H:i:s');
        $transaction->save();
    }
}
