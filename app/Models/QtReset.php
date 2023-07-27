<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class QtReset extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'qt_resets';

    protected $guarded = [];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
}
