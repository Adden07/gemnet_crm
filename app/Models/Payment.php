<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model
{
    use HasFactory,DianujHashidsTrait, SoftDeletes;

    protected $table = 'payments';
    protected $guarded  = [];

    public $timestamps = false;

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function transaciton(){
        return $this->hasOne(Transaction::class, 'transaction_id', 'transaction_id');
    }

}
