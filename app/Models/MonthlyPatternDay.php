<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPatternDay extends Model
{
    protected $fillable = ['monthly_pattern_id', 'day_of_month', 'shift_id'];
}
