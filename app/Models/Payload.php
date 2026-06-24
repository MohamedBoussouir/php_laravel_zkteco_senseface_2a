<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

class Payload extends Model
{
    use Prunable;

    protected $fillable = ['device_sn', 'endpoint', 'content'];

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(7));
    }
}
