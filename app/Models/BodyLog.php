<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'logged_at',
        'weight_kg',
        'body_fat_percentage',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'logged_at' => 'date',
        'weight_kg' => 'float',
        'body_fat_percentage' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
