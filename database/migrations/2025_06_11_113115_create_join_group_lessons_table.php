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
        Schema::create('join_group_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_lessons_id")->references("id")->on("group_lessons")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_group_lessons');
    }
};
