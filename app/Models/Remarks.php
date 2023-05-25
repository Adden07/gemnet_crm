<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Remarks extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'remarks';

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
