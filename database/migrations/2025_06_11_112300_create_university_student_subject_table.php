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
        Schema::create('university_student_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("university_subject")->references("id")->on("university_subjects")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_student_subject');
    }
};
