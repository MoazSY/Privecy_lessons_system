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
        Schema::create('student_card_charging', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('admin')->onDelete('cascade');
            $table->foreignId('students_id')->references('id')->on('students')->onDelete('cascade');
            $table->integer('card_charging');
            $table->dateTime('charging_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_card_charging');
    }
};
