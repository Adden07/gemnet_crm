<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Customize extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'customizes';

    // protected $fillable = ['admin_id', 'data'];

    protected $casts = [
        'admin_id'  => 'integer',
        'data'      => 'object',
    ];
}
