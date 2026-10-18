<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('gender')->default('male'); // male, female, other
            $table->date('birth_date')->nullable();
            $table->integer('age')->default(25);
            $table->float('height_cm')->default(170);
            $table->float('current_weight_kg')->default(70);
            $table->float('target_weight_kg')->default(68);
            $table->string('fitness_goal')->default('hypertrophy'); // lose_fat, hypertrophy, endurance, stay_active
            $table->integer('weekly_target_days')->default(4);
            $table->json('preferred_sports')->nullable(); // ['gym', 'running', 'calisthenics', 'cycling']
            $table->string('preferred_unit')->default('kg'); // kg, lbs
            $table->integer('default_rest_seconds')->default(90);
            $table->string('theme')->default('dark'); // dark, light
            $table->string('accent_badge')->default('lightning');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
