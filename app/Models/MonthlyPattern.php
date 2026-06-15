<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPattern extends Model
{
    protected $guarded = [];
    
    public function days()
    {
        return $this->hasMany(MonthlyPatternDay::class, 'monthly_pattern_id');
    }
}
