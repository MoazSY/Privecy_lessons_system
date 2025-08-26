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
    $table->text("sesstion_url");
    $table->dateTime("start_time")->nullable(); // وقت بداية الجلسة
    $table->integer("teacher_duration_minutes")->nullable(); // مدة حضور المدرس بالدقائق
    $table->dateTime("end_time");
    $table->dateTime("teacher_join_time")->nullable(); // وقت انضمام المدرس
    $table->dateTime("student_join_time")->nullable(); // وقت انضمام الطالب
    $table->morphs("S_or_G_lesson");
    $table->string("meeting_id")->nullable(); // ID غرفة Jitsi
    $table->enum("status", ["scheduled", "active", "completed", "cancelled"])->default("scheduled");
    $table->string("recording_path")->nullable(); // مسار التسجيل
    
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
