<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class PkgQueue extends Model
{
    use HasFactory, DianujHashidsTrait;

    public $table = 'pkq_queue';

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
