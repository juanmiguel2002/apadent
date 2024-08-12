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
            $table->bigIncrements('id'); // id - bigint(20) UNSIGNED NOT NULL
            $table->unsignedBigInteger('trat_id'); // trat_id - bigint(20) UNSIGNED NOT NULL
            $table->string('name'); // name - varchar(255) NOT NULL
            $table->enum('status', ['Set Up', 'En proceso', 'Pausado', 'Finalizado'])->default('Set Up'); // status - enum
            $table->timestamps(); // created_at and updated_at - timestamp NULL DEFAULT NULL

            // Foreign key constraint (if 'trat_id' references another table)
             $table->foreign('trat_id')->references('id')->on('tratamientos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
