<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Ledger extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'transactions';

    public $timestamps = false;

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function receiver(){
        return $this->belongsTo(Admin::class, 'receiver_id', 'id');
    }
}
