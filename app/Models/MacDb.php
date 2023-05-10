<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class MacDb extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'macdb';

    public $timestamps = false;
}
