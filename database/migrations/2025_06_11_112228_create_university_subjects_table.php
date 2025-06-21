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
        Schema::create('university_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId("university_stage_id")->references("id")->on("university_stage")->onDelete("cascade");
            $table->string("subject_name");
            $table->string("about_subject")->nullable();
            $table->string("subject_cover_image")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_subjects');
    }
};
