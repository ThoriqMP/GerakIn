<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'avatar',
        'onboarding_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class)->orderBy('sort_order');
    }

    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }

    public function bodyLogs(): HasMany
    {
        return $this->hasMany(BodyLog::class)->orderBy('logged_at', 'desc');
    }

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function customExercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function hasCompletedOnboarding(): bool
    {
        return !is_null($this->onboarding_completed_at);
    }

    /**
     * Calculate consecutive weekly workout streak
     */
    public function calculateWeeklyStreak(): int
    {
        $targetDays = $this->profile?->weekly_target_days ?? 3;
        $now = Carbon::now();
        $streak = 0;

        // Check each week going backwards
        for ($i = 0; $i < 52; $i++) {
            $weekStart = (clone $now)->subWeeks($i)->startOfWeek();
            $weekEnd = (clone $now)->subWeeks($i)->endOfWeek();

            $completedCount = $this->workoutSessions()
                ->where('is_completed', true)
                ->whereBetween('started_at', [$weekStart, $weekEnd])
                ->count();

            // For the current ongoing week ($i === 0), if target is reached, count +1.
            // If current week is ongoing and target not reached yet, it doesn't break previous streak!
            if ($i === 0) {
                if ($completedCount >= $targetDays) {
                    $streak++;
                }
                continue;
            }

            if ($completedCount >= $targetDays) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Check and award badges based on milestones
     */
    public function checkAndAwardBadges(): array
    {
        $awarded = [];
        $completedSessions = $this->workoutSessions()->where('is_completed', true)->get();
        $sessionCount = $completedSessions->count();
        $totalVolume = $completedSessions->sum('total_volume_kg');

        // Badge: First Sweat
        if ($sessionCount >= 1 && !$this->badges()->where('badge_code', 'first_sweat')->exists()) {
            $this->badges()->create([
                'badge_code' => 'first_sweat',
                'title' => 'First Sweat',
                'description' => 'Menyelesaikan sesi latihan pertamamu di GerakIn!',
                'icon' => 'zap',
                'unlocked_at' => now(),
            ]);
            $awarded[] = 'First Sweat';
        }

        // Badge: 10,000 Kg Club
        if ($totalVolume >= 10000 && !$this->badges()->where('badge_code', '10k_club')->exists()) {
            $this->badges()->create([
                'badge_code' => '10k_club',
                'title' => '10,000 Kg Club',
                'description' => 'Total angkatan akumulatif telah melampaui 10.000 kg!',
                'icon' => 'award',
                'unlocked_at' => now(),
            ]);
            $awarded[] = '10,000 Kg Club';
        }

        // Badge: 50,000 Kg Club (Titan)
        if ($totalVolume >= 50000 && !$this->badges()->where('badge_code', '50k_club')->exists()) {
            $this->badges()->create([
                'badge_code' => '50k_club',
                'title' => 'Titan Lifter',
                'description' => 'Mencapai total volume angkatan 50.000 kg!',
                'icon' => 'shield',
                'unlocked_at' => now(),
            ]);
            $awarded[] = 'Titan Lifter';
        }

        // Badge: PR Smasher
        $hasPR = WorkoutSet::whereHas('workoutSession', function ($q) {
            $q->where('user_id', $this->id)->where('is_completed', true);
        })->where('is_pr', true)->exists();

        if ($hasPR && !$this->badges()->where('badge_code', 'pr_smasher')->exists()) {
            $this->badges()->create([
                'badge_code' => 'pr_smasher',
                'title' => 'PR Smasher',
                'description' => 'Memecahkan Rekor Pribadi (Personal Record) baru!',
                'icon' => 'trending-up',
                'unlocked_at' => now(),
            ]);
            $awarded[] = 'PR Smasher';
        }

        // Badge: Weekly Consistency Streak
        if ($this->calculateWeeklyStreak() >= 3 && !$this->badges()->where('badge_code', 'streak_master')->exists()) {
            $this->badges()->create([
                'badge_code' => 'streak_master',
                'title' => 'Streak Master',
                'description' => 'Konsisten mencapai target latihan 3 minggu berturut-turut!',
                'icon' => 'flame',
                'unlocked_at' => now(),
            ]);
            $awarded[] = 'Streak Master';
        }

        return $awarded;
    }
}
