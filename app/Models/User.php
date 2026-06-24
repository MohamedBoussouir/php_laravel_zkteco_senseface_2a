<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['uid', 'user_id', 'name', 'email', 'password', 'role', 'device_id', 'fingerprints', 'work_pattern_id', 'photo', 'card_number', 'has_face', 'last_sync_time'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function workPattern()
    {
        return $this->belongsTo(WorkPattern::class);
    }

    public function monthlyPattern()
    {
        return $this->belongsTo(MonthlyPattern::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    protected static function booted()
    {
        // Removed static::deleting so that attendance and schedules are kept on soft delete
    }
}

