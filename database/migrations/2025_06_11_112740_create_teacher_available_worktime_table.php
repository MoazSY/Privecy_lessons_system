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
        Schema::create('teacher_available_worktime', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->string("workingDay");
            $table->time("start_time");
            $table->time("end_time");
            $table->time("break_duration_lessons");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_available_worktime');
    }
};
