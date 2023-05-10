<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Admin;
use DataTables;

class InvoiceController extends Controller
{
    public function index(Request $req){

        if(\CommonHelpers::rights('enabled-finance','enabled-invoices')){
            return redirect()->route('admin.home');
        }

        $data = array(
            'title' => 'Invoices',
            'invoices'  => Invoice::with(['admin','user','package'])
                                    ->when(isset($req->from_date),function($query) use ($req){//when from and to date set
                                        // $query->whereBetween('created_at',[$req->from_date,$req->to_date]);
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);
                                    },function($query){
                                        $query->whereDay('created_at',date('Y-m-d'));
                                    })->when(isset($req->package_id),function($query) use ($req){//when package id is set
                                        $query->where('pkg_id',hashids_decode($req->package_id));
                                    })->when(isset($req->type),function($query) use ($req){//when type is iset
                                        $query->where('type',$req->type);
                                    })->when((isset($req->franchise_id) && !isset($req->dealer_id) && !isset($req->subdealer_id)),function($query) use ($req){//get by franchise id
                                        $query->where('admin_id',hashids_decode($req->franchise_id));
                                    })->when((isset($req->franchise_id) && isset($req->dealer_id) && !isset($req->subdealer_id)),function($query) use ($req){//get by dealer
                                        $query->where('admin_id',hashids_decode($req->dealer_id));
                                    })->when((isset($req->franchise_id) && isset($req->dealer_id) && isset($req->subdealer_id)),function($query) use ($req){//get by subdealer id
                                        $query->where('admin_id',hashids_decode($req->subdealer_id));
                                    })->when(auth()->user()->user_type != 'admin',function($query){
                                        $query->whereIn('admin_id',\CommonHelpers::getChildIds());
                                    })->orderBy('admin_id','DESC')->orderBy('id','DESC')->paginate(1000)->withQueryString(),
                                    
            'invoices_total'  => Invoice::select('type','total_cost')->when(isset($req->from_date),function($query) use ($req){//when from and to date set
                                        // $query->whereBetween('created_at',[$req->from_date,$req->to_date]);
                                        $query->whereDate('created_at', '>=', $req->from_date)->whereDate('created_at', '<=', $req->to_date);

                                    },function($query){
                                        $query->whereDay('created_at',date('Y-m-d'));
                                    })->when(isset($req->package_id),function($query) use ($req){//when package id is set
                                        $query->where('pkg_id',hashids_decode($req->package_id));
                                    })->when(isset($req->type),function($query) use ($req){//when type is iset
                                        $query->where('type',$req->type);
                                    })->when((isset($req->franchise_id) && !isset($req->dealer_id) && !isset($req->subdealer_id)),function($query) use ($req){//get by franchise id
                                        $query->where('admin_id',hashids_decode($req->franchise_id));
                                    })->when((isset($req->franchise_id) && isset($req->dealer_id) && !isset($req->subdealer_id)),function($query) use ($req){//get by dealer
                                        $query->where('admin_id',hashids_decode($req->dealer_id));
                                    })->when((isset($req->franchise_id) && isset($req->dealer_id) && isset($req->subdealer_id)),function($query) use ($req){//get by subdealer id
                                        $query->where('admin_id',hashids_decode($req->subdealer_id));
                                    })->when(auth()->user()->user_type != 'admin',function($query){
                                        $query->whereIn('admin_id',\CommonHelpers::getChildIds());
                                    })->orderBy('admin_id','DESC')->orderBy('id','DESC')->get(),
                                    
            'packages'    => Package::orderBy('id','DESC')->get(),
            'franchises'  => Admin::where('user_type','franchise')->latest()->get(),
            'user_type'   => auth()->user()->user_type,      
        );
        // dd($data['invoices_total']);
        if(auth()->user()->user_type != 'admin'){
            if(auth()->user()->user_type == 'franchise'){
                $data['childs'] = Admin::where('added_to_id',auth()->user()->id)->where('user_type','dealer')->get();
            }elseif(auth()->user()->user_type == 'dealer'){
                $data['childs'] = Admin::where('added_to_id',auth()->user()->id)->where('user_type','sub_dealer')->get();
            }
        }
        
        return view('admin.invoice.all_invoices')->with($data);
    }

    //get subdealers of specified dealer
    public function getSubdealers($id){
        if(isset($id) && !empty($id)){
            $subdealers = Admin::where('added_to_id',hashids_decode($id))->where('is_active','active')->get();
            $html = view('admin.invoice.get_subdealer',compact('subdealers'))->render();
            
            return response()->json([
                'html'  => $html
            ]);
        }
        abort(404);
    }

    public function payInvoice($id){
        if(isset($id) && !empty($id)){
            $invoice = Invoice::findOrFail(hashids_decode($id));
            $invoice->paid = 1;
            $invoice->save();

            return response()->json([
                'success'   => 'Invoice Paid Successfully',
                'reload'    => TRUE,
            ]);
        }
    }

    public function unpaidInvoice(Request $req){
        
        // if(\CommonHelpers::rights('enabled-finance','enabled-payments')){
        //     return redirect()->route('admin.home');
        // }

        // $admin_ids = Admin::where('user_type','admin')->get()->pluck('id')->toArray();
        
        if($req->ajax()){
            $data =        Invoice::with(['user'])
                                    ->when(auth()->user()->user_type != 'admin', function($query){
                                        $query->where('admin_id', auth()->id());
                                    })
                                    ->where('paid', 0);
                                            

            return DataTables::of($data)
                                ->addIndexColumn()
                                // ->setRowId('DT_RowIndex',function(){
                                //     return '<input type="checkbox">';
                                // })
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
                                ->addColumn('username',function($data){
                                    // return $data->user->username;
                                    return "<a href=".route('admin.users.profile',['id'=>@hashids_encode($data->user->id)])." target='_blank'>".@$data->user->username."</a>";

                                })
                                ->addColumn('address',function($data){
                                    return $data->user->address;
                                })
                                ->addColumn('paid',function($data){
                                    $paid =     "<label class='switch mb-0'>
                                                    <input type='checkbox' class='nopopup' onchange='ajaxRequest(this)' data-url=".route('admin.accounts.invoices.pay_invoice',['id'=>$data->hashid]).">
                                                    <span class='slider round'></span>
                                                </label>";
                                    return $paid;            
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
                                                        // ->orWhere('type', 'LIKE', "%$search%")
                                                        // ->orWhere('amount', 'LIKE', "%$search%")
                                                        // ->orWhere('old_balance', 'LIKE', "%$search%")
                                                        // ->orWhere('new_balance', 'LIKE', "%$search%")
                                                        // ->orWhereHas('receiver',function($q) use ($search){
                                                        //         $q->whereLike(['name','username'], '%'.$search.'%');

                                                        //     })
                                                        ->orWhereHas('user',function($q) use ($search){
                                                            $q->whereLike(['name','username','address'], '%'.$search.'%');

                                                        });      
                                        });
                                    }
                                })
                                ->orderColumn('DT_RowIndex', function($q, $o){
                                    $q->orderBy('created_at', $o);
                                    })
                                // ->editColumn('DT_RowIndex', function ($data) {
                                //     return '<input type="checkbox">';
                                // })
                                ->rawColumns(['date', 'paid', 'added_by', 'type', 'username',])
                                ->make(true);

        }
        $data = array(
            'title' => 'Payments',
        );
        return view('admin.invoice.unpaid_invoices')->with($data);
    }
}
