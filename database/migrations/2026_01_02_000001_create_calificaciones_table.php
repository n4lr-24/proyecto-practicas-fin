<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estudiante_nombre', 120);
            $table->unsignedInteger('inasistencias')->default(0);
            $table->decimal('calificacion', 4, 2);
            $table->string('equivalencia', 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
