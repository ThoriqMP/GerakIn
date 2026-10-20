<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Sesi Lari');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->decimal('distance_km', 8, 3)->default(0.000);
            $table->integer('avg_pace_seconds')->default(0); // seconds per km
            $table->integer('best_pace_seconds')->nullable();
            $table->integer('calories_burned')->default(0);
            $table->json('route_coordinates')->nullable(); // array of [lat, lng, alt, time]
            $table->json('km_splits')->nullable(); // array of [{km: 1, duration: 320, pace: "5:20"}]
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('runs');
    }
};
