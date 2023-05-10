<?php

namespace App\Models;

class Permission extends BaseModel
{
    protected $table = 'permissions';
    public $timestamps = false;

    public function type(){
        return $this->belongsTo(PermissionType::class, 'type_id');
    }
}