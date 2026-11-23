<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->unique(['alumno_user_id', 'unidad'], 'calificaciones_alumno_unidad_unique');
        });
    }

    public function down(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropUnique('calificaciones_alumno_unidad_unique');
        });
    }
};
