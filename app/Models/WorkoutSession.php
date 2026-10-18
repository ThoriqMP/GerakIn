<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'routine_id',
        'title',
        'started_at',
        'timer_started_at',
        'ended_at',
        'duration_seconds',
        'total_volume_kg',
        'notes',
        'mood_rating',
        'is_completed',
        'client_session_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'timer_started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'total_volume_kg' => 'float',
        'mood_rating' => 'integer',
        'is_completed' => 'boolean',
    ];

    /**
     * Check if workout timer has been initiated or has completed sets
     */
    public function isTimerActive(): bool
    {
        return !is_null($this->timer_started_at) || $this->sets()->where('is_completed', true)->exists();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class);
    }

    public function sets(): HasMany
    {
        return $this->hasMany(WorkoutSet::class)->orderBy('id');
    }

    /**
     * Recalculate total volume from all completed sets
     */
    public function recalculateVolume(): float
    {
        $volume = 0.0;
        foreach ($this->sets()->where('is_completed', true)->get() as $set) {
            $volume += ($set->weight_kg * $set->reps);
        }
        $this->update(['total_volume_kg' => $volume]);
        return $volume;
    }

    /**
     * Get unique primary muscles trained in this session
     */
    public function getMusclesTrained(): array
    {
        $exerciseIds = $this->sets()->pluck('exercise_id')->unique();
        return Exercise::whereIn('id', $exerciseIds)
            ->pluck('primary_muscle')
            ->unique()
            ->values()
            ->toArray();
    }
}
