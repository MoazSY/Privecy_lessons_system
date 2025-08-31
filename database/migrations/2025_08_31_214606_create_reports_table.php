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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId("admin_id")->references("id")->on("admin")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("lesson_session")->references("id")->on("lesson_session")->onDelete("cascade");
            $table->string("type_report");
            $table->string("reference_report_path")->nullable();
            $table->longText("descreption")->nullable();
            $table->dateTime("time_report")->nullable();
            $table->enum("state",["Open", "In_Review", "Resolved"])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report');
    }
};
