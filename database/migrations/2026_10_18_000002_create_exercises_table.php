<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('primary_muscle'); // chest, back, legs, shoulders, arms, core, cardio
            $table->string('secondary_muscle')->nullable();
            $table->string('equipment')->default('barbell'); // barbell, dumbbell, machine, cable, bodyweight, cardio
            $table->string('category')->default('strength'); // strength, cardio
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
