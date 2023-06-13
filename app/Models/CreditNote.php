<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class CreditNote extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'credit_notes';

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function transaction(){
        return $this->belongsTo(Ledger::class, 'transaction_id', 'id');
    }
    
}
