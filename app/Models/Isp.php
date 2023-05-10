<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Isp extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'isps';

    protected $fillable = ['city_id', 'company_name', 'poc_name', 'mobile', 'address'];

    protected $casts = [
        'city_id'  =>  'integer',
        'company_name'  => 'string',
        'poc_name'  => 'string',
        'mobile'    => 'string',
        'address'   => 'string'
    ];

    public function cities(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
}
