<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class Invoice extends Model
{
    use HasFactory,DianujHashidsTrait;

    protected $table = 'invoices';

    public $timestamps = false;

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'pkg_id', 'id');
    }
}
