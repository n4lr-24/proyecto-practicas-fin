<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('computadoras', function (Blueprint $table) {
            $table->id();
            $table->string('clave_equipo', 50)->unique();
            $table->string('etiqueta', 120);
            $table->string('sala', 100);
            $table->text('caracteristicas');
            $table->string('estado', 30)->default('Disponible');
            $table->timestamps();
        });

        Schema::create('solicitudes_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('computadora_id')->constrained('computadoras')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('inicio');
            $table->time('fin');
            $table->string('estado', 30)->default('Activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_reserva');
        Schema::dropIfExists('computadoras');
    }
};
