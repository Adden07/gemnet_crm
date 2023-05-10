<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadacctArchive extends Model
{
    use HasFactory;
    
    protected $connection ='mysqlSecondConnection';


    protected $table = 'radacct_archive';

    public $timestamps = false;
}
