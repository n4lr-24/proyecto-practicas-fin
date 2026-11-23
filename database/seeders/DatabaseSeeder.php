<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Jefa Administradora',
                'email' => 'jefa.admin@practicas.edu.mx',
                'rol' => RolUsuario::ADMINISTRADOR,
            ],
            [
                'name' => 'Operador Auxiliar',
                'email' => 'operador.aux@practicas.edu.mx',
                'rol' => RolUsuario::EMPLEADO,
            ],
            [
                'name' => 'Docente Web',
                'email' => 'docente.web@practicas.edu.mx',
                'rol' => RolUsuario::PROFESOR,
            ],
            [
                'name' => 'Estudiante Curso',
                'email' => 'estudiante.curso@practicas.edu.mx',
                'rol' => RolUsuario::ALUMNO,
            ],
            [
                'name' => 'Reservas Uno',
                'email' => 'reservas.uno@practicas.edu.mx',
                'rol' => RolUsuario::ESTUDIANTE,
            ],
            [
                'name' => 'Jugador Alfa',
                'email' => 'jugador.alfa@practicas.edu.mx',
                'rol' => RolUsuario::JUGADOR,
            ],
        ];

        foreach ($usuarios as $datos) {
            User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('clave12345'),
                    'rol' => $datos['rol'],
                ]
            );
        }

        $this->call([
            CalificacionSeeder::class,
            ArticuloSeeder::class,
            ComputadoraSeeder::class,
        ]);
    }
}
