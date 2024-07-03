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
        Schema::create('archivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trat_etapa_id');
            $table->string('ruta');
            $table->timestamps();

            $table->foreign('trat_etapa_id')->references('id')->on('trat_etapas')->onDelete('cascade');
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
