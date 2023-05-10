<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class UserTmp extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'users_tmp';

    public $timestamps = false;

    protected $dates = ['expiration'];

    public function packages(){
        return  $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'username', 'username');
    }
}
