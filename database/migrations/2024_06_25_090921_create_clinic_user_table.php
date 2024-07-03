$clinicaUser->clinicas()->attach($clinica->id);<?php

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
        Schema::create('clinica_user', function (Blueprint $table) {
            $table->unsignedBigInteger('clinica_id');
            $table->unsignedBigInteger('user_id');
            $table->primary(['clinica_id', 'user_id']);

            $table->foreign('clinica_id')->references('id')->on('clinicas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinica_user');
    }
};
