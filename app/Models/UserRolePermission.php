<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\DianujHashidsTrait;

class UserRolePermission extends Model
{
    use HasFactory, DianujHashidsTrait;

    protected $table = 'user_role_permissions';

    protected $fillable = ['role_name', 'slug', 'permissions'];
    
    protected $casts = [
        'role_name' => 'string',
        // 'slug'      => 'string',
        'permissions'   => 'array'
    ];
}
