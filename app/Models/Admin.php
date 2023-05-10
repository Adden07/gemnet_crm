<?php

namespace App\Models;

use App\Permissions\HasPermissionsTrait;
use App\Traits\DianujHashidsTrait;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable,  DianujHashidsTrait, HasPermissionsTrait;

    protected $guard = 'admin';

    protected $table = 'admins';

    protected $casts = [
        'user_permissions' => 'object',
    ];
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'email', 'password', 'firstname', 'lastname', 'mobile_no', 'image', 'usertype', 'is_verify'
    // ];
    protected $fillable = ['isp_id', 'name', 'username', 'password', 'email', 'nic', 'city_id', 'address'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute()
    {
        return (!empty($this->name)) ? ucwords($this->name) : ucwords($this->firstname . ' ' . $this->lastname);
    }

    public function getCompleteNameAttribute()
    {
        return $this->full_name;
    }

    public function user_emails()
    {
        return $this->morphMany('App\Models\UsersEmail', 'user');
    }

    public function getIsAdminAttribute()
    {
        return $this->user_type == 'admin';
    }

    public function scopeAuthors($query){
        return $query->where('user_type', 'author')->get();
    }

    public function scopeNotifiableAdmins($query){
        return $query->where('user_type', 'admin')->get();
    }

    public function scopeSupportAdmin($query){
        return $query->whereEmail('support@shipit4us.com')->first();
        // return $query->whereUserType('admin')->first();
    }

    public function scopeBillingAdmin($query){
        return $query->whereEmail('billing@shipit4us.com')->first();
    }

    public function city(){
        return $this->belongsTo(City::class,'city_id','id');
    }

    public function area(){
        return $this->belongsTo(Area::class,'area_id','id');
    }

    // public function isp(){
    //     return $this->belongsTo(Isp::class,'isp_id','id');
    // }

    public function areas(){
        return $this->belongsToMany(Area::Class,'admin_area','admin_id','area_id')->withTimestamps();
    }

    public function parent(){
        return $this->belongsTo(Admin::class, 'added_to_id', 'id');
    }

    public function users(){
        return $this->hasMany(User::class, 'admin_id', 'id');
    }

    public function dealers(){
        return $this->hasMany(Admin::class, 'added_to_id', 'id');
    }

    public function subdealers(){
        return $this->hasMany(Admin::class, 'added_to_id', 'id');
    }

    public function frnachiseSubDealers(){
        return $this->hasManyThrough(Admin::class, Admin::class, 'added_to_id', 'added_to_id', 'id', 'id');
    }
}
