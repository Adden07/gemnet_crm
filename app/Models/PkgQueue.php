<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class PkgQueue extends Model
{
    use HasFactory, DianujHashidsTrait;

    public $table = 'pkq_queue';
}
