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
        Schema::create('teacher_rating', function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->integer("rate");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_rating');
    }
};
