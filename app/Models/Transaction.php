<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Transaction extends Model
{
    use HasFactory;
    
    protected $table = 'transactions';
    protected $guarded = [];
    public $timestamps = false;

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
