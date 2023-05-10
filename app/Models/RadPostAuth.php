<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadPostAuth extends Model
{
    use HasFactory;

    protected $table = 'radpostauth';

    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
