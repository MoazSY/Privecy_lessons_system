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
        Schema::table('lesson_reservation', function (Blueprint $table) {
                if (!Schema::hasColumn('lesson_reservation', 'duration')) {
                $table->integer('duration')->default(60);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_reservation', function (Blueprint $table) {
            //
             $table->dropColumn('duration');
        });
    }
};
