<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_session_id',
        'exercise_id',
        'set_number',
        'set_type',
        'weight_kg',
        'reps',
        'distance_km',
        'duration_seconds',
        'rpe',
        'is_pr',
        'is_completed',
    ];

    protected $casts = [
        'set_number' => 'integer',
        'weight_kg' => 'float',
        'reps' => 'integer',
        'distance_km' => 'float',
        'duration_seconds' => 'integer',
        'rpe' => 'integer',
        'is_pr' => 'boolean',
        'is_completed' => 'boolean',
    ];

    public function workoutSession(): BelongsTo
    {
        return $this->belongsTo(WorkoutSession::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

    /**
     * Calculate estimated One-Rep Max using Epley formula
     * 1RM = weight * (1 + reps / 30)
     */
    public function getEstimatedOneRepMaxAttribute(): float
    {
        if ($this->reps <= 0 || $this->weight_kg <= 0) {
            return 0.0;
        }

        if ($this->reps === 1) {
            return (float) $this->weight_kg;
        }

        return round($this->weight_kg * (1 + ($this->reps / 30.0)), 1);
    }
}
