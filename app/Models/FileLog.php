<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class FileLog extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'file_logs';

    protected $guarded = [];  


    public $timestamps = false;
}
