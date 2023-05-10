<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Setting extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'settings';

    // protected $fillable = [];
    // protected $casts = [
    //     'admin_id'      => 'integer',
    //     'city_id'       => 'integer',
    //     'logo'          => 'string',
    //     'favicon'       => 'string',
    //     'company_name'  => 'string',
    //     'email'         => 'string',
    //     'slogan'        => 'string',
    //     'mobile'        => 'string',
    //     'address'       => 'string',
    //     'country'       => 'string',
    //     'zipcode'       => 'string',
    //     'copyright'     => 'string',
    // ];
}
