<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;
class RadCheck extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'radcheck';

    public $timestamps = false;
}
