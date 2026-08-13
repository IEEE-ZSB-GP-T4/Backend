<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
protected $fillable = [
    'course_id',
    'title',
    'description',
    'deadline',
    'estimated_hours',
    'priority',
    'status',
    'completed_at',
    'reminder_sent_at',
    'second_reminder_sent_at',
];

protected $casts = [
    'deadline' => 'datetime',
    'completed_at' => 'datetime',
    'reminder_sent_at' => 'datetime',
    'second_reminder_sent_at' => 'datetime',
    'estimated_hours' => 'decimal:2',
];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
