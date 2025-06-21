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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string("firstName")->nullable();
            $table->string("lastName")->nullable();
            $table->date("birthdate")->nullable();
            $table->text("image")->nullable();
            $table->string("email")->unique()->nullable();
            $table->string("password")->nullable();
            $table->text("idintification_image")->nullable();
            $table->string("phoneNumber")->unique();
            $table->enum("gender",["male","female"])->nullable();
            $table->string("accountNumber")->unique()->nullable();
            $table->text("about_him")->nullable();
            $table->boolean('is_profile_completed')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
