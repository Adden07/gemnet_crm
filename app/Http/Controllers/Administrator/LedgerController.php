<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;
class LedgerController extends Controller
{
    public function index(){
        $data = array(
            'title' => 'Ledger',
            'users' => User::latest()->get(),
        );
        return view('admin.ledger.index')->with($data);
    }

    public function userLedger(Request $req){
        $req->validate([
            'user_id'   => ['required']
        ]);
        
        $data = array(
            'title'     => 'User Ledger',
            'payments'  => Payment::where('receiver_id', hashids_decode($req->user_id))->get(),
            'invoices'  => Invoice::where('user_id', hashids_decode($req->user_id))->get(),
            'users'     => User::latest()->get(),
            'is_ledger' => true 
        );
        return view('admin.ledger.index')->with($data);
    }

    public function pdf(Request $req){
        $data = array(
            'payments'  => Payment::where('receiver_id', hashids_decode($req->pdf_user_id))->get(),
            'invoices'  => Invoice::where('user_id', hashids_decode($req->pdf_user_id))->get(),
        );
        $pdf = PDF::loadView('admin.ledger.pdf', $data);
        return $pdf->download('ledger.pdf');
    }
}
