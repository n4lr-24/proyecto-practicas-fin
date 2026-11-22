<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\User;
use Illuminate\Database\Seeder;

class CalificacionSeeder extends Seeder
{
    public function run(): void
    {
        $alumnoDemo = User::where('email', 'estudiante.curso@practicas.edu.mx')->first();

        $registros = [
            [
                'alumno_user_id' => $alumnoDemo?->id,
                'estudiante_nombre' => 'Estudiante Curso',
                'inasistencias' => 1,
                'calificacion' => 9.60,
            ],
            [
                'alumno_user_id' => $alumnoDemo?->id,
                'estudiante_nombre' => 'Estudiante Curso (Unidad 2)',
                'inasistencias' => 2,
                'calificacion' => 8.80,
            ],
            [
                'alumno_user_id' => null,
                'estudiante_nombre' => 'Ana Sofía Martínez',
                'inasistencias' => 0,
                'calificacion' => 9.80,
            ],
            [
                'alumno_user_id' => null,
                'estudiante_nombre' => 'Carlos Eduardo López',
                'inasistencias' => 3,
                'calificacion' => 9.20,
            ],
            [
                'alumno_user_id' => null,
                'estudiante_nombre' => 'Luis Fernando Torres',
                'inasistencias' => 5,
                'calificacion' => 8.40,
            ],
            [
                'alumno_user_id' => null,
                'estudiante_nombre' => 'María Guadalupe Reyes',
                'inasistencias' => 8,
                'calificacion' => 7.20,
            ],
        ];

        foreach ($registros as $item) {
            Calificacion::create([
                ...$item,
                'equivalencia' => Calificacion::obtenerEquivalencia((float) $item['calificacion']),
            ]);
        }
    }
}
