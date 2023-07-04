<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
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
        if(CommonHelpers::rights('enabled-finance','view-credit-note')){
            return redirect()->route('admin.home');
        }

        if($req->ajax()){
                return DataTables::of(CreditNote::query())
                                    ->addIndexColumn()
                                    ->addColumn('date',function($data){
                                        return date('d-M-Y H:i:s', strtotime($data->created_at));
                                    })
                                    ->addColumn('reciever_name',function($data){
                                        return "<a href=".route('admin.users.profile',['id'=>hashids_encode(@$data->user->id)])." target='_blank'>".@$data->user->username."</a>";
                                    })
                                    ->addColumn('invoice',function($data){
                                        return "<a href=".route('admin.accounts.invoices.get_invoice', ['id'=>$data->invoice->hashid])." target='_blank'>".@$data->invoice->invoice_id."</a>";
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
                                    ->addColumn('action', function($data){
                                        $html = '';
                                        if(auth()->user()->can('edit-credit-note')){
                                            $html .= "<a href=".route('admin.accounts.credit_notes.edit',['id'=>$data->hashid])." class='btn btn-warning btn-xs waves-effect waves-light' title='Edit'>
                                                    <i class='icon-pencil'></i>
                                                </a>";
                                        }
                                        

                                       if(auth()->user()->can('delete-credit-note')){
                                            $html .= " <button type'button' onclick='ajaxRequest(this)' data-url=".route('admin.accounts.credit_notes.delete', ['id'=>$data->hashid])." class='btn btn-danger btn-xs waves-effect waves-light'>
                                                <span class='btn-label'><i class='icon-trash'></i>
                                                </span>Delete
                                            </button>";
                                       }
                                    return $html;
                                   })
                                   ->filter(function($query) use ($req){
                                    // dd($req->all());
                                    if(isset($req->user_id) && $req->user_id != 'all'){
                                        $query->where('user_id',hashids_decode($req->user_id));
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
                                            $search = $req->search;
                                            $search_query->orWhere('created_at', 'LIKE', "%$search%")
                                                        ->orWhere('amount', 'LIKE', "%$search%")
                                                        ->orWhereHas('user',function($q) use ($search){
                                                                $q->whereLike(['name','username'], '%'.$search.'%');
                                                            })
                                                        ->orWhereHas('admin',function($q) use ($search){
                                                            $q->whereLike(['name','username'], '%'.$search.'%');
                                                        })
                                                        ->orWhereHas('invoice',function($q) use ($search){
                                                            $q->whereLike(['invoice_id'], '%'.$search.'%');
                                                        });      
                                        });
                                    }

                                    
                                })
                                ->rawColumns(['reciever_name', 'action', 'invoice'])
                                ->make(true);
            }
        $data = array(
            'title' => 'Credit Note',
            'users' => User::latest()->get(),
            'admins'        => Admin::where('user_type','!=','superadmin')->get(),
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
        $old_balance = 0;
        $new_balance = 0;

        if(isset($validated['credit_note_id']) && !empty($validated['credit_note_id'])){//edit credit note
        
            $credit_note = CreditNote::findOrFail(hashids_decode($validated['credit_note_id']));
            $transaction = Ledger::findOrFail($credit_note->transaction_id);
            // User::findOrFail(hashids_decode($validated['user_id']))->decrement('user_current_balance', $credit_note->amount);
            $user        = User::findOrFail(hashids_decode($validated['user_id']));
            $user->decrement('user_current_balance', $credit_note->amount);
            // dd($user->user_current_balance);
            $msg         = 'User updated successfully';
        }else{//insert new credit note row

            $user       = User::findOrFail(hashids_decode($validated['user_id']));
            $transaction = new Ledger;
            $credit_note= new CreditNote;
            
            $transaaction_id =  rand(111111111,999999999);
            $transaction->transaction_id    = $transaaction_id;
            $msg       = 'User added successfully';
        }
       
 

        $transaction->admin_id          = auth()->id();
        $transaction->user_id           = $user->id;
        $transaction->amount            = $validated['amount'];
        $transaction->old_balance       = $user->user_current_balance;
        $transaction->new_balance       = $user->user_current_balance+$validated['amount'];
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
            'success'       => $msg,
            'redirect'  => route('admin.accounts.credit_notes.index')
        ]);
        
    }

    public function getUserInvoices($id){
        
        $html = '';
        $invoices = Invoice::where('user_id', hashids_decode($id))
                    ->whereBetween('created_at', [now()->subMonth(7)->format('Y-m-d'), now()->format('Y-m-d')])
                    ->latest()
                    ->get()->map(function($data) use (&$html){
                        $html .= "<option value='$data->hashid'>$data->invoice_id</option>";
                    });
        return response()->json([
            'html'  => $html,
        ]);
    }

    public function edit($id){
        
        if(CommonHelpers::rights('enabled-finance','edit-credit-note')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title'             => 'Credit Note',
            'users'             => User::latest()->get(),
            'edit_credit_note'  => CreditNote::findOrFail(hashids_decode($id)),
            'admins'        => Admin::where('user_type','!=','superadmin')->get(),
            'is_update'         => true
        );
        $data['invoices'] = Invoice::where('user_id', $data['edit_credit_note']->user_id)->get();

        return view('admin.credit_note.index')->with($data);
    }

    public function delete($id){

        if(CommonHelpers::rights('enabled-finance','delete-credit-note')){
            return redirect()->route('admin.home');
        }

        $credit_note = CreditNote::findOrFail(hashids_decode($id));
        $user        = User::findOrFail($credit_note->user_id)->decrement('user_current_balance',$credit_note->amount);
        $credit_note->transaction()->delete();
        $credit_note->delete();

        return response()->json([
            'success'   => 'Credit note deleted successfully',
            'reload'    => true
        ]);
    }
}
