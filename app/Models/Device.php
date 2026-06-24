<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['sn', 'name', 'ip_address', 'status', 'last_seen', 'time_offset', 'metadata'];
    //
}


