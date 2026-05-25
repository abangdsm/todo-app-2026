<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'due_date',
        'is_completed',
        'order'
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke sub_tasks
    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    // Accessor warna badge
    public function getPriorityBadgeAttribute()
    {
        return match ($this->priority) {
            'high' => 'bg-red-500 text-white',
            'medium' => 'bg-yellow-500 text-black',
            'low' => 'bg-green-500 text-white',
            default => 'bg-gray-500 text-white',
        };
    }
}
