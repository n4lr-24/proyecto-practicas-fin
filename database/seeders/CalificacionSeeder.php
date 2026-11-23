<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\Calificacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CalificacionSeeder extends Seeder
{
    public function run(): void
    {
        $alumnoDemo = User::where('email', 'estudiante.curso@practicas.edu.mx')->first();

        $alumnosExtra = [
            'ana.martinez@practicas.edu.mx' => 'Ana Sofía Martínez',
            'carlos.lopez@practicas.edu.mx' => 'Carlos Eduardo López',
            'luis.torres@practicas.edu.mx' => 'Luis Fernando Torres',
            'maria.reyes@practicas.edu.mx' => 'María Guadalupe Reyes',
        ];

        $cuentas = [];
        foreach ($alumnosExtra as $email => $nombre) {
            $cuentas[$nombre] = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'password' => Hash::make('clave12345'),
                    'rol' => RolUsuario::ALUMNO,
                ]
            );
        }

        $registros = [
            [
                'alumno_user_id' => $alumnoDemo?->id,
                'estudiante_nombre' => 'Estudiante Curso',
                'unidad' => 1,
                'inasistencias' => 1,
                'calificacion' => 9.60,
            ],
            [
                'alumno_user_id' => $alumnoDemo?->id,
                'estudiante_nombre' => 'Estudiante Curso',
                'unidad' => 2,
                'inasistencias' => 2,
                'calificacion' => 8.80,
            ],
            [
                'alumno_user_id' => $cuentas['Ana Sofía Martínez']->id,
                'estudiante_nombre' => 'Ana Sofía Martínez',
                'unidad' => 1,
                'inasistencias' => 0,
                'calificacion' => 9.80,
            ],
            [
                'alumno_user_id' => $cuentas['Carlos Eduardo López']->id,
                'estudiante_nombre' => 'Carlos Eduardo López',
                'unidad' => 2,
                'inasistencias' => 2,
                'calificacion' => 9.20,
            ],
            [
                'alumno_user_id' => $cuentas['Luis Fernando Torres']->id,
                'estudiante_nombre' => 'Luis Fernando Torres',
                'unidad' => 3,
                'inasistencias' => 3,
                'calificacion' => 8.40,
            ],
            [
                'alumno_user_id' => $cuentas['María Guadalupe Reyes']->id,
                'estudiante_nombre' => 'María Guadalupe Reyes',
                'unidad' => 4,
                'inasistencias' => 5,
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
