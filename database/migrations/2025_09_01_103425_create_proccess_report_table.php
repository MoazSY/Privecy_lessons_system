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
        Schema::create('proccess_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId("admin_id")->references("id")->on("admin")->onDelete("cascade");
            $table->foreignId("report_id")->references("id")->on("reports")->onDelete("cascade");
            $table->enum("proccess_method",["warning","block","disscount","nothing"]);
            $table->enum("block_type",["hour","day","week"])->nullable();
            $table->integer("block_duaration_value")->nullable();
            $table->float("disscount_percentage_value")->nullable();
            $table->dateTime("response_time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proccess_report');
    }
};
