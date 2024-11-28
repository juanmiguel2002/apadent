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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->unsignedBigInteger('user_id'); // Relación con usuarios
            $table->text('mensaje'); // Mensaje del usuario
            $table->unsignedBigInteger('etapa_id');

            $table->timestamps(); // Campos de creación y actualización
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Relación con usuarios en la tabla users (cascade para eliminar el mensaje si el usuario es eliminado)
            $table->foreign('etapa_id')->references('id')->on('etapas')->onDelete('cascade');
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
