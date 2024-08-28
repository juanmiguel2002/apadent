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
        Schema::create('tratamiento_etapa', function (Blueprint $table) {
            // $table->bigIncrements('id');
            $table->unsignedBigInteger('trat_id');
            $table->unsignedBigInteger('etapa_id');
            $table->string('orden');
            $table->timestamps();

            $table->foreign('trat_id')->references('id')->on('tratamientos')->onDelete('cascade');
            $table->foreign('etapa_id')->references('id')->on('etapas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamiento_etapa');
    }
};
