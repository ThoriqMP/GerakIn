<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'birth_date',
        'age',
        'height_cm',
        'current_weight_kg',
        'target_weight_kg',
        'fitness_goal',
        'weekly_target_days',
        'preferred_sports',
        'preferred_unit',
        'default_rest_seconds',
        'theme',
        'accent_badge',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'height_cm' => 'float',
        'current_weight_kg' => 'float',
        'target_weight_kg' => 'float',
        'weekly_target_days' => 'integer',
        'preferred_sports' => 'array',
        'default_rest_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
