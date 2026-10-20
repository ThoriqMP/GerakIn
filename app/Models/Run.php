<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Run extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'started_at',
        'ended_at',
        'duration_seconds',
        'distance_km',
        'avg_pace_seconds',
        'best_pace_seconds',
        'calories_burned',
        'route_coordinates',
        'km_splits',
        'notes',
        'is_completed',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
        'distance_km' => 'float',
        'avg_pace_seconds' => 'integer',
        'best_pace_seconds' => 'integer',
        'calories_burned' => 'integer',
        'route_coordinates' => 'array',
        'km_splits' => 'array',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format duration as HH:MM:SS or MM:SS
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    /**
     * Format pace as M'SS" /km
     */
    public function getFormattedPaceAttribute(): string
    {
        if ($this->avg_pace_seconds <= 0 || $this->avg_pace_seconds > 3600) {
            return "--'--\" /km";
        }

        $minutes = floor($this->avg_pace_seconds / 60);
        $seconds = $this->avg_pace_seconds % 60;

        return sprintf("%d'%02d\" /km", $minutes, $seconds);
    }

    /**
     * Format best pace as M'SS" /km
     */
    public function getFormattedBestPaceAttribute(): string
    {
        if (!$this->best_pace_seconds || $this->best_pace_seconds <= 0) {
            return "--'--\" /km";
        }

        $minutes = floor($this->best_pace_seconds / 60);
        $seconds = $this->best_pace_seconds % 60;

        return sprintf("%d'%02d\" /km", $minutes, $seconds);
    }

    /**
     * Format distance e.g. "5.24 km"
     */
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 2) . ' km';
    }

    /**
     * Format date e.g. "Sel, 20 Okt 2026 • 06:15"
     */
    public function getFormattedDateAttribute(): string
    {
        if (!$this->started_at) {
            return '-';
        }

        return $this->started_at->translatedFormat('D, d M Y • H:i');
    }

    /**
     * Detect time of day for title helper (Pagi, Siang, Sore, Malam)
     */
    public function getTimeOfDayAttribute(): string
    {
        if (!$this->started_at) {
            return 'Lari';
        }

        $hour = $this->started_at->hour;
        if ($hour >= 4 && $hour < 11) {
            return 'Lari Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            return 'Lari Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            return 'Lari Sore';
        } else {
            return 'Lari Malam';
        }
    }
}
