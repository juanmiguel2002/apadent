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
        Schema::create('carpetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedBigInteger('carpeta_id')->nullable();
            $table->unsignedBigInteger('clinica_id');
            $table->timestamps();
            
            $table->foreign('carpeta_id')->references('id')->on('carpetas')->onDelete('cascade');
            $table->foreign('clinica_id')->references('id')->on('clinicas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carpetas');
    }
};
