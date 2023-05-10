<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Helpers\CommonHelpers;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, SoftDeletes, DianujHashidsTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'firstname', 'lastname', 'mobile_no', 'image', 'usertype', 'is_verify', 'userrole'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'all_ratings' => 'object',
        'customer_info'=> 'object'
    ];


    protected $shipper_types = ['shipper_individual', 'shipper_business'];

    public function getFullNameAttribute()
    {
        if($this->user_type == 'shipper_individual' || empty($this->company_name)){
            return ucwords($this->firstname . ' ' . $this->lastname[0] . '.');
        }
        return $this->company_name;
    }

    public function getCompleteNameAttribute()
    {
        return ucwords($this->firstname . ' ' . $this->lastname . '.');
    }

    public function getNameForAdminAttribute()
    {
        if($this->user_type == 'shipper_individual'){
            return ucwords($this->firstname . ' ' . $this->lastname);
        }
        return ucwords($this->company_name ?? $this->firstname . ' ' . $this->lastname);
    }

    public function getGetUserTypeAttribute()
    {
        return ucwords(str_replace("_", " ", $this->user_type));
    }

    public function user_details(){
        return $this->hasOne('App\Models\UserDetails');
    }

    public function user_emails(){
        return $this->morphMany('App\Models\UsersEmail', 'user');
    }

    public function sendPasswordResetNotification($token){
        $user = $this->where('email', \Request::get('email'))->first();
        $arr = array(
            'email' => \Request::get('email'),
            'token' => $token,
            'user_id' => $user->id,
            'user_type' => 'user',
            'name' => $user->name
        );

        CommonHelpers::send_email('reset', $arr, $arr['email'], 'Reset Password');
    }

    public function is_approved(){
        return ($this->approved_at) ?  true : false;
    }

    public function getIsShipperAttribute(){
        return in_array($this->user_type, $this->shipper_types);
    }

    public function getIsProviderAttribute(){
        return !in_array($this->user_type, $this->shipper_types);
    }

    public function scopeShippers($query)
    {
        return $query->whereIn('user_type', $this->shipper_types);
    }

    public function scopeProviders($query)
    {
        return $query->whereNotIn('user_type', $this->shipper_types);
    }

    public function getShipperTypeAttribute(){
        $shipper = explode('_', $this->user_type);
        return ucwords($shipper[1] ?? $this->get_user_type);
    }

    public function getIsIndividualShipperAttribute(){
        return $this->user_type == 'shipper_individual';
    }

    public function getIsBusinessShipperAttribute(){
        return $this->user_type == 'shipper_business';
    }

    public function getIsCarrierAttribute()
    {
        return $this->user_type == 'carrier';
    }

    public function getIsIndependentDriverAttribute()
    {
        return $this->user_type == 'independent_driver';
    }

    public function getIsFreightForwarderAttribute()
    {
        return $this->user_type == 'freight_forwarder';
    }

    public function getIsBrokerAttribute()
    {
        return $this->user_type == 'broker';
    }

    public function user_defaults()
    {
        return $this->hasOne('App\Models\UserDefault');
    }

    public function getCompanyAttribute()
    {
        return $this->company_name;
        if($this->is_instant_carrier){
            return $this->company_name;
        }
        $pad = 'xxxxx';
        return ucwords($this->company_name[0].''. $pad.' '. $this->company_name[strlen($this->company_name) - 1]);
    }

    public function violationComplain(){
        return $this->morphOne('App\Models\ViolationComplains', 'typeable');
    }

    public function user_bank(){
        return $this->hasOne('App\Models\UserBankAccount');
    }

    public function active_marketplace_quotes(){
        return $this->hasMany('App\Models\MarketplaceQuote', 'provider_id')->where('status', 'pending');
    }

    public function scopeInsuranceCompany($query){
        return $query->whereEmail('insurance@shipit4us.com');
    }

    public function scopeShipmentNotifiableProviders($query){
        return $query->whereNotIn('user_type', $this->shipper_types)->whereIsInstantCarrier(0)->get();
    }

    public function shipments(){
        return $this->hasMany('App\Models\Shipment', 'user_id');
    }

    public function quotes(){
        return $this->hasMany('App\Models\Quote', 'user_id');
    }
}