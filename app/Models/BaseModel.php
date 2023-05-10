<?php

namespace App\Models;

use App\Traits\DianujHashidsTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use DianujHashidsTrait;
    protected $guarded = [];
}
