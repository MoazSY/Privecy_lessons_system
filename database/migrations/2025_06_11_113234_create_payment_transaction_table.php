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
        Schema::create('payment_transaction', function (Blueprint $table) {
            $table->id();
            // $table->string("payment_transaction_ref");
            $table->foreignId("teacher_id")->references("id")->on("teacher")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->nullableMorphs("S_or_G_lesson");
            $table->float("amount");
            $table->float('commission')->default(0.15);
            $table->float('commission_value')->nullable();
            $table->enum("currency",["sy","$"])->default("sy");
            // $table->string("payment_method");//E_click
            // $table->enum("getway_response",["fail","success"]);
            // $table->dateTime("payment_transaction_time");
            // $table->text("descreption")->nullable();
            $table->integer('Admin_Id');
            $table->boolean("admin_payout_teacher");
            // $table->string("payout_transaction_ref")->nullable();
            // $table->dateTime("payout_transaction_time")->nullable();
            $table->boolean("teacher_disscount")->nullable();
            $table->float("disscount_percentage")->nullable();
            $table->float("teacher_amount_final")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transaction');
    }
};
