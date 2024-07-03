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
        Schema::create('trat_etapas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tratamiento_id');
            $table->unsignedBigInteger('paciente_id');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('tratamiento_id')->references('id')->on('tratamientos')->onDelete('cascade');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trat_etapas');
    }
};
