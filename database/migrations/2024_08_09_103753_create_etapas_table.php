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
        //
        Schema::create('etapas', function (Blueprint $table) {
            $table->id(); // id - bigint(20) UNSIGNED NOT NULL
            $table->string('name'); // name - varchar(255) NOT NULL
            $table->date('fecha_ini');
            $table->date('fecha_fin');
            $table->enum('status', ['Set Up', 'En proceso', 'Pausado', 'Finalizado'])->default('Set Up'); // status - enum
            $table->date('revision');
            $table->unsignedBigInteger('fases_id'); // trat_id - bigint(20) UNSIGNED NOT NULL
            $table->timestamps(); // created_at and updated_at - timestamp NULL DEFAULT NULL

            $table->foreign('fases_id')->references('id')->on('fases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('etapas');
    }
};
