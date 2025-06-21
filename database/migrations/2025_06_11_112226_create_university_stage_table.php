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
        Schema::create('university_stage', function (Blueprint $table) {
            $table->id();
            $table->string("university_type");
            $table->string("university_branch");
            $table->string("college_name");
            $table->string("study_year");
            $table->boolean("specialize")->nullable();
            $table->string("specialize_name")->nullable();
            $table->string("semester");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('university_stage');
    }
};
