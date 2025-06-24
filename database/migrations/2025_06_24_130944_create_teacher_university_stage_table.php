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
            $table->foreignId("teacher_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("university_stage_id")->references("id")->on("university_stage")->onDelete("cascade");
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
