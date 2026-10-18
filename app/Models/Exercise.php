<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'primary_muscle',
        'secondary_muscle',
        'equipment',
        'category',
        'instructions',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function routineExercises(): HasMany
    {
        return $this->hasMany(RoutineExercise::class);
    }

    public function workoutSets(): HasMany
    {
        return $this->hasMany(WorkoutSet::class);
    }

    public function isBodyweight(): bool
    {
        return in_array($this->equipment, ['bodyweight', 'none', 'cardio']) 
            || in_array($this->category, ['calisthenics', 'cardio']);
    }

    public function defaultStartingWeight(): float
    {
        if ($this->isBodyweight()) {
            return 0.0;
        }

        return match ($this->equipment) {
            'barbell' => 20.0,
            'dumbbell' => 10.0,
            'cable' => 15.0,
            'machine' => 20.0,
            default => 0.0,
        };
    }

    /**
     * Get user's Personal Record for this exercise (max weight or max reps for bodyweight)
     */
    public function getPersonalRecordForUser(int $userId): ?WorkoutSet
    {
        $query = WorkoutSet::where('exercise_id', $this->id)
            ->where('is_completed', true)
            ->whereHas('workoutSession', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('is_completed', true);
            });

        if ($this->isBodyweight()) {
            // Check if user has any weighted sets
            $weightedPr = (clone $query)->where('weight_kg', '>', 0)
                ->orderBy('weight_kg', 'desc')
                ->orderBy('reps', 'desc')
                ->first();

            if ($weightedPr) {
                return $weightedPr;
            }

            // Otherwise PR is by max reps
            return $query->orderBy('reps', 'desc')
                ->orderBy('weight_kg', 'desc')
                ->first();
        }

        return $query->orderBy('weight_kg', 'desc')
            ->orderBy('reps', 'desc')
            ->first();
    }

    /**
     * Get the sets from the user's most recent workout session containing this exercise
     */
    public function getPreviousSetsForUser(int $userId): array
    {
        $lastSession = WorkoutSession::where('user_id', $userId)
            ->where('is_completed', true)
            ->whereHas('sets', function ($q) {
                $q->where('exercise_id', $this->id);
            })
            ->latest('started_at')
            ->first();

        if (!$lastSession) {
            return [];
        }

        return WorkoutSet::where('workout_session_id', $lastSession->id)
            ->where('exercise_id', $this->id)
            ->orderBy('set_number')
            ->get()
            ->toArray();
    }
}
