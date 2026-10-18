<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
            $table->integer('set_number')->default(1);
            $table->string('set_type')->default('normal'); // warmup, normal, drop, failure
            $table->float('weight_kg')->default(0);
            $table->integer('reps')->default(0);
            $table->float('distance_km')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->tinyInteger('rpe')->nullable(); // 1 to 10
            $table->boolean('is_pr')->default(false);
            $table->boolean('is_completed')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sets');
    }
};
