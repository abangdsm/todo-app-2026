<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SubTask;
use App\Models\Task;
// use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔥 Relasi ke tasks (HARUS return HasMany)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // 🔥 Relasi ke sub_tasks
    public function subTasks(): HasMany
    {
        return $this->hasMany(SubTask::class);
    }

    // 🔥 Method update streak
    public function updateStreak(): void
    {
        $today = now()->toDateString();
        $lastDate = $this->last_completion_date;

        if ($lastDate == $today) {
            return;
        }

        $yesterday = now()->subDay()->toDateString();

        if ($lastDate == $yesterday) {
            $this->current_streak += 1;
        } else {
            $this->current_streak = 1;
        }

        if ($this->current_streak > $this->best_streak) {
            $this->best_streak = $this->current_streak;
        }

        $this->last_completion_date = $today;
        $this->saveQuietly(); // Save without triggering events
    }

    // Accessor buat sorting prioritas di query
    public function getPriorityRawAttribute()
    {
        return match ($this->priority) {
            'high' => 3,
            'medium' => 2,
            'low' => 1,
            default => 0,
        };
    }
}
