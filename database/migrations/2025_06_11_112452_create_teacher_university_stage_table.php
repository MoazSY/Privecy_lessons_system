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
        Schema::create('teacher_university_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->foreignId("university_subjects_id")->references("id")->on("university_subjects")->onDelete("cascade");
            $table->time("lesson_duration");
            $table->float("lesson_price");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_university_stage');
    }
};
