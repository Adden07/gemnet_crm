<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Sms extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'sms';

    protected $guarded = [];
}
