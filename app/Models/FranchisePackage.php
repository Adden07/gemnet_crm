<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class FranchisePackage extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $tabel = 'franchise_packages';

    public function admin(){
        return $this->belongsTo(Admin::class, 'edit_by_id', 'id');
    }

    public function package(){
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
    public function parent(){
        return $this->belongsTo(FranchisePackage::class, 'edit_by_id', 'added_to_id');
    }
    public function childs(){
        return $this->hasMany(FranchisePackage::class, 'edit_by_id', 'added_to_id');
    }
    
}
