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
        Schema::table('user_quiz_progress', function (Blueprint $table) {
            Schema::table('user_quiz_progress', function (Blueprint $table) {
                $table->string('user_id')->change(); // Modify user_id to string
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quiz_progress', function (Blueprint $table) {
            //
        });
    }
};
