<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Area extends Model
{
    use DianujHashidsTrait;

    protected $table = 'areas';

    protected $guarded = [];

    public function city(){
        return $this->belongsTo(City::class,'city_id','id');
    }

    public function area(){
        return $this->belongsTo(Area::class,'area_id','id');
    }

    public function subAreas(){
        return $this->hasMany(Area::class, 'area_id', 'id');
    }
}