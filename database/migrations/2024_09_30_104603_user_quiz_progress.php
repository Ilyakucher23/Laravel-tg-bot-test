<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_quiz_progress', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('answers'); // Set a default empty JSON object
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_quiz_progress');
    }
};
