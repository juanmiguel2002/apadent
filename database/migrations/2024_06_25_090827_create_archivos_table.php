+3206<?php

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
        Schema::create('archivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ruta', 255);
            $table->string('tipo', 55);
            $table->string('extension', 255);
            $table->unsignedBigInteger('etapa_id');
            $table->unsignedBigInteger('carpeta_id');
            $table->timestamps();

            $table->foreign('etapa_id')->references('id')->on('etapas')->onDelete('cascade');
            $table->foreign('carpeta_id')->references('id')->on('carpetas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos');
    }
};
