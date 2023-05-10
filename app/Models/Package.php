<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;


class Package extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'packages';

    public $timestamps = false;

    public function users(){
        return  $this->hasMany(User::class, 'package', 'id');
    }

    public function default_package(){
        return $this->belongsTo(Package::class, 'd_pkg', 'id');
    }
}
