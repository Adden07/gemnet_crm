<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class RemarkType extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $guarded = [];

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
