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
        Schema::create('group_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->integer("student_number");
            $table->float("lesson_price");
            $table->dateTime("lesson_date");
            $table->morphs("subjectable");
            $table->text("descreption")->nullable();
            $table->float("lesson_duration");
            $table->dateTime("start_create_group_lessone");
            $table->dateTime("end_time_join");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_lessons');
    }
};
