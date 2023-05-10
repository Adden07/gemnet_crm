<?php

namespace App\Models;

class PermissionType extends BaseModel
{
    protected $table = 'permission_types';
    public $timestamps = false;

    public function permissions(){
        return $this->hasMany(Permission::class, 'type_id');
    }
}