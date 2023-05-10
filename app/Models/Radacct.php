<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Radacct extends Model
{
    use HasFactory;

    protected $table = 'radacct';

    public function user(){
        return  $this->belongsTo(User::class, 'username', 'username');
    }

    public $timestamps = false;
}
