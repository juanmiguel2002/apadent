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
        // Schema::create('mensajes', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('etapa_id');
        //     $table->unsignedBigInteger('user_id');
        //     $table->text('mensaje');
        //     $table->timestamps();

        //     $table->foreign('etapa_id')->references('id')->on('etapas')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con usuarios
            $table->text('mensaje'); // Mensaje del usuario
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade'); // Relación con pacientes
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->onDelete('set null'); // Relación con tratamientos
            $table->foreignId('etapa_id')->nullable()->constrained('etapas')->onDelete('set null'); // Relación con etapas
            $table->timestamps(); // Campos de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
