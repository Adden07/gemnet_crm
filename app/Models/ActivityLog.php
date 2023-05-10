<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = ['user_id', 'user_ip', 'activity'];

    protected $casts = [
        'user_id'  => 'integer',
        'user_ip'  => 'string',
        'activity' => 'string',
    ];

    public function user(){
        return $this->belongsTo(Admin::class, 'user_id', 'id');
    }
}
