<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class UserPackageRecord extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'user_package_records';

    public $timestamps = false;
    
    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function last_package(){
        return $this->belongsTo(Package::class, 'last_package_id', 'id');
    }
}
