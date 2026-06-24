<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPatternDay extends Model
{
    protected $fillable = ['pattern_id', 'day_of_week', 'shift_id'];
    //
}

