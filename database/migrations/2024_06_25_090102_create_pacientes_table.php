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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('num_paciente')->unique();
            $table->string('name');
            $table->date('fecha_nacimiento');
            $table->string('email');
            $table->string('telefono');
            $table->date('revision')->nullable();
            $table->string('observacion')->nullable();
            $table->string('obser_cbct')->nullable();
            $table->string('odontograma_obser')->nullable();

            $table->unsignedBigInteger('clinica_id');
            $table->timestamps();

            $table->foreign('clinica_id')->references('id')->on('clinicas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
