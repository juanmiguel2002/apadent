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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('users_id');
            $table->text('mensaje');
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('etapa_id');
            $table->timestamps();

            $table->index('users_id');
            $table->index(['paciente_id', 'etapa_id']);

            $table->foreign('users_id')
                  ->references('id')
                  ->on('users')->onDelete('cascade');

            $table->foreign(['paciente_id', 'etapa_id'])
                  ->references(['paciente_id', 'etapa_id'])
                  ->on('paciente_etapas')->onDelete('cascade');
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
