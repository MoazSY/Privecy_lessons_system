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
        Schema::create('student_school_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("school_stage_id")->references("id")->on("school_stage")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_school_stage');
    }
};
