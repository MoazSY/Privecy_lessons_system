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
        Schema::create('delivery_cash_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('admin')->onDelete('cascade');
            $table->foreignId('teacher_id')->references('id')->on('teacher')->onDelete('cascade');
            $table->integer("cash_value");
            $table->dateTime('delivery_time');
            $table->boolean('teacher_acceptance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_cash_teacher');
    }
};
