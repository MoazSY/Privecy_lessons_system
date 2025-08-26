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
        Schema::create('lesson_session_presences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_session_id');
            $table->unsignedBigInteger('user_id'); // المدرس أو الطالب
            $table->string('role')->default('teacher'); // teacher | student
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            $table->foreign('lesson_session_id')->references('id')->on('lesson_session')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_session_presences');
    }
};
