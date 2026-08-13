<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'available_hours',
        'generated_plan',
    ];

    protected $casts = [
        'available_hours' => 'decimal:1',
        'generated_plan'  => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}