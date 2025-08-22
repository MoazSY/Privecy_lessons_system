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
        // يجب اضافة ال  lesson_reservation ,date time
        Schema::create('lesson_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->morphs("subjectable");
            $table->string("sesstion_url");
            $table->time("teacher_time_session");
            $table->morphs("S_or_G_lesson");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_session');
    }
};
