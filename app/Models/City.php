<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class City extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'cities';

    public function areas(){
        return $this->hasMany(Area::class, 'city_id', 'id');
    }

    // public function userCity(){
    //     return $this->belongsTo(User::class, 'city_id', 'id');
    // }
}
