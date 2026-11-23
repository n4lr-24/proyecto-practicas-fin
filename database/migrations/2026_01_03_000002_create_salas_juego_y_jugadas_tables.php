<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salas_juego', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retador_x_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('retador_o_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('turno', 1)->default('X');
            $table->json('casillas');
            $table->string('estado', 20)->default('esperando');
            $table->foreignId('ganador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('jugadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sala_id')->constrained('salas_juego')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('marca', 1);
            $table->unsignedTinyInteger('posicion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jugadas');
        Schema::dropIfExists('salas_juego');
    }
};
