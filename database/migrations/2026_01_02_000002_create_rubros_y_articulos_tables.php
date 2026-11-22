<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100)->unique();
            $table->timestamps();
        });

        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150)->unique();
            $table->text('detalle');
            $table->foreignId('rubro_id')->constrained('rubros')->cascadeOnDelete();
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('stock')->default(0);
            $table->string('disponibilidad', 20)->default('Disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
        Schema::dropIfExists('rubros');
    }
};
