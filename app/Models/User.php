<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $guarded = [];

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
        static::deleting(function ($user) {
            // Delete all attendances for this user (user_id here refers to the device PIN)
            \App\Models\Attendance::where('user_id', $user->user_id)->delete();
            
            // Delete all schedules for this user
            \App\Models\Schedule::where('user_id', $user->user_id)->delete();
        });
    }
}

