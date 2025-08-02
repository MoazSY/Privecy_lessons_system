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
        Schema::create('lesson_reservation', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->dateTime("reservation_time");
            // $table->integer("duration");
            $table->string("reservation_day");
            $table->enum("state_reservation",["Watting_approve","accepted","rejectd"]);
            $table->morphs("subjectable");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_reservation');
    }
};
