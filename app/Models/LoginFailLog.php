<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginFailLog extends Model
{
    use HasFactory;

    protected $table = 'login_fail_logs';

    protected $fillable = ['username', 'ip'];
    
    public function user(){
        return $this->belongsTo(LoginFailLog::class, 'username', 'username');
    }
}
