<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    use HasFactory;

    protected $table = 'franchises';

    // protected $fillable = [
    //     'edit_by_id',
    //     'city_id',
    //     'area-id',
    //     'setting_id',
    //     'name',
    //     'username',
    //     'email',
    //     'password',
    //     'nic',
    //     'address',
    //     'user_type',
    //     'credit_limit'
    // ];

    // protected $casts = [
    //     'edit_by_id'    => 'integer',
    //     'city_id'       => 'integer',
    //     'area-id'       => 'integer',
    //     'setting_id'    => 'integer',
    //     'name'          => 'string',
    //     'username'      => 'string',
    //     'email'         => 'string',
    //     'password'      => 'string',
    //     'nic'           => 'string',
    //     'address'       => 'string',
    //     'user_type'     => 'string',
    //     'credit_limit'  => 'double'
    // ];
}
