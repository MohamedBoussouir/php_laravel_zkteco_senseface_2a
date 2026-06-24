<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkPattern extends Model
{
    protected $fillable = ['name'];
    
    public function days()
    {
        return $this->hasMany(WorkPatternDay::class, 'pattern_id');
    }
}
