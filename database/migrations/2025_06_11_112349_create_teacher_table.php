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
        Schema::create('teacher', function (Blueprint $table) {
            $table->id();
            $table->string("firstName");
            $table->string("lastName");
            $table->string("image")->nullable();
            $table->string("identification_image")->nullable();
            $table->date("birthdate");
            $table->string("phoneNumber")->unique();
            $table->string("url_certificate_file");
            $table->string("about_teacher")->nullable();
            $table->string("email")->unique()->nullable();
            $table->string("password")->nullable();
            $table->enum("gender",["male","female"])->nullable();
            $table->string("account_number")->nullable();
            $table->rememberToken();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher');
    }
};
