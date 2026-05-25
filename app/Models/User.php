<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SubTask;
use App\Models\Task;
// use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    // Relasi ke tasks
    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('priority_raw')->orderBy('order');
    }

    // Relasi ke sub_tasks
    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    // Update streak (dipanggil setiap kali selesai task/subtask)
    public function updateStreak()
    {
        $today = now()->toDateString();
        $lastDate = $this->last_completion_date;

        if ($lastDate == $today) {
            // Udah dihitung hari ini, gak usah ngapa-ngapain
            return;
        }

        $yesterday = now()->subDay()->toDateString();

        if ($lastDate == $yesterday) {
            // Streak lanjut
            $this->current_streak += 1;
        } else {
            // Streak reset
            $this->current_streak = 1;
        }

        // Update best streak
        if ($this->current_streak > $this->best_streak) {
            $this->best_streak = $this->current_streak;
        }

        $this->last_completion_date = $today;
        $this->save();
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
