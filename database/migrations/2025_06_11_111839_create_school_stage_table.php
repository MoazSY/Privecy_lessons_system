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
        Schema::create('school_stage', function (Blueprint $table) {
            $table->id();
            $table->enum("school_stage",["primary", "preparatory", "secondary"]);
            $table->enum("className",["first","second","third","fourth","fifth","sixth"]);
            $table->boolean("specialize")->nullable();
            $table->enum("secondary_school_branch",['scientific','literary','vocational'])->nullable();
            $table->string("vocational_type")->nullable();
            $table->enum("semester",["one","tow"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_stage');
    }
};
