<?php

namespace App\Http\Controllers\Administrator;

use App\Helpers\CommonHelpers;
use App\Http\Controllers\Controller;
use App\Models\DepostSlip;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepositSlipController extends Controller
{
    public function index(){
        // dd(DepostSlip::select('deposit_date')->get()->pluck('deposit_date')->toArray());
        $data = array(
            'title' => 'Deposit Slip',
            'payment_dates' => Payment::select('created_at')
                            ->whereNotIn(\DB::raw('DATE(created_at)'),DepostSlip::select('deposit_date')->get()->pluck('deposit_date')->toArray())
                            ->where('type', 'cash')
                            ->whereDate('created_at','>=', date('2023-06-01'))
                            ->whereDate('created_at', '!=', now())
                            ->groupBy(\DB::raw('Day(created_at), MONTH(created_at), YEAR(created_at)'))
                            ->orderBy('created_at', 'DESC')
                            ->get(),
            'deposit_slips' => DepostSlip::latest()->get(),
        );
        return view('admin.deposit_slip.index')->with($data);
    }

    public function store(Request $req){
        $rules = [
            'deposit_date'  => ['required', 'date'],
            'amount'        => ['required', 'integer'],
            'image'         => ['required', 'mimes:jpeg,jpg,png', 'max:2000']
        ];

        $validator = Validator::make($req->all(), $rules);

        if($validator->fails()){
            return ['errors'=>$validator->errors()];
        }

        if($req->hasFile('image')){ //store image
            $image  = CommonHelpers::uploadSingleFile($req->image, 'admin_uploads/deposit_slips/', "png,jpeg,jpg", 2000);
        }

        if(isset($req->deposit_slip_id) && !empty($req->deposit_slip_id)){//update the record

        }else{//insert new record
            $deposit = new DepostSlip;
            $msg     = 'Deposit slip added successfullys';
        }
        $deposit->admin_id = auth()->id();
        $deposit->amount =  $req->amount;
        $deposit->image  = $image ?? '';
        $deposit->deposit_date   = $req->deposit_date;
        $deposit->save();

        return response()->json([
            'success'   => $msg,
            'redirect'  => route('admin.accounts.deposit_slips.index')
        ]);
    }

    public function getPaymentDateAmount($date){
        return response()->json([
            'amount'    => Payment::whereDate('created_at', $date)->where('type', 'cash')->sum('amount'),
        ]);
    }
}
