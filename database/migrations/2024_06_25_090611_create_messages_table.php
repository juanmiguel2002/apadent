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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trat_etapa_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->timestamps();

            $table->foreign('trat_etapa_id')->references('id')->on('trat_etapas')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
