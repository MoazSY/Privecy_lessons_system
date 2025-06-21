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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->foreignId("admin_id")->references("id")->on("admin")->onDelete("cascade");
            $table->foreignId("student_id")->references("id")->on("students")->onDelete("cascade");
            $table->foreignId("lesson_session")->references("id")->on("lesson_session")->onDelete("cascade");
            $table->json("type_report");
            $table->string("reference_report_url")->nullable();
            $table->longText("descreption");
            $table->dateTime("time_report");
            $table->enum("state",["Open", "In_Review", "Resolved"]);
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
