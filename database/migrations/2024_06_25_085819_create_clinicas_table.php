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
        Schema::create('clinicas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('responsable'); // (rol doctor)
            $table->string('email');
            $table->string('telefono');
            $table->string('direccion');
            // $table->string('cp');
            $table->string('localidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinics');
    }
};
