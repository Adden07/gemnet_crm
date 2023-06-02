<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class User extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'users';

    // protected $fillable = [
    //     'admin_id',
    //     'city_id',
    //     'name',
    //     'username',
    //     'password',
    //     'portal_pass',
    //     'nic',
    //     'mobile',
    //     'user_status',
    //     'address',
    //     'nic_front',
    //     'nic_back'
    // ];

    // protected $casts = [
    //     'admin_id'      => 'integer',
    //     'city_id'       => 'integer',
    //     'name'          => 'string',
    //     'username'      => 'string',
    //     'password'      => 'string',
    //     'portal_pass'   => 'string',
    //     'nic'           => 'string',
    //     'mobile'        => 'string',
    //     'user_status'   => 'string',
    //     'address'       => 'string',
    //     'nic_front'     => 'string',
    //     'nic_back'      => 'string'
    // ];

    public function city(){
        return $this->belongsTo(City::class,'city_id','id');
    }

    public function area(){
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    public function subarea(){
        return $this->belongsTo(Area::class, 'subarea_id', 'id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class,'admin_id','id');
    }
    //this function used to get the expiration date of pacakge
    public function rad_check(){
        return $this->belongsTo(RadCheck::class, 'username', 'username')->where('attribute','Expiration');
    }
    
    public function user_package_record(){
        return $this->hasMany(UserPackageRecord::class, 'user_id', 'id');
    }

    public function packages(){
        return $this->belongsTo(Package::class, 'package', 'id');
    }

    public function current_package(){
        return $this->belongsTo(Package::class, 'c_package', 'id');
    }

    public function primary_package(){
        return $this->belongsTo(Package::class, 'package', 'id');
    }

    public function lastPackage(){
        return $this->belongsTo(Package::class, 'last_package', 'id');
    }

    public function activation(){
        return $this->belongsTo(Admin::class, 'activation_by', 'id');
    }

    public function renew(){
        return $this->belongsTo(Admin::class, 'renew_by', 'id');
    }

    public function salePerson(){
        return $this->belongsTo(Admin::class,'fe_id','id');
    }

    public function fieldEngineer(){
        return $this->belongsTo(Admin::class,'sales_id','id');
    }

    public function remark(){
        return $this->hasMany(Remarks::class, 'user_id', 'id');
    }

    public function queue(){
        return $this->hasMany(PkgQueue::class, 'user_id', 'id');
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'receiver_id', 'id');
    }

}
