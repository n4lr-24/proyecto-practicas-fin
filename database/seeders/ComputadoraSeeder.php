<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\Computadora;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ComputadoraSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'reservas.dos@practicas.edu.mx'],
            [
                'name' => 'Reservas Dos',
                'password' => Hash::make('clave12345'),
                'rol' => RolUsuario::ESTUDIANTE,
            ]
        );

        $equipos = [
            ['PC-LAB-01', 'Estación Dell OptiPlex 1', 'Laboratorio A', 'Intel Core i5, 16GB RAM, SSD 512GB', 'Disponible'],
            ['PC-LAB-02', 'Estación Dell OptiPlex 2', 'Laboratorio A', 'Intel Core i5, 16GB RAM, SSD 512GB', 'Disponible'],
            ['PC-LAB-03', 'Estación HP ProDesk 3', 'Laboratorio A', 'Intel Core i7, 16GB RAM, SSD 1TB', 'Disponible'],
            ['PC-LAB-04', 'Estación HP ProDesk 4', 'Laboratorio A', 'Intel Core i7, 16GB RAM, SSD 1TB', 'Disponible'],
            ['PC-LAB-05', 'Estación Lenovo ThinkCentre 5', 'Laboratorio B', 'AMD Ryzen 5, 16GB RAM, SSD 512GB', 'Disponible'],
            ['PC-LAB-06', 'Estación Lenovo ThinkCentre 6', 'Laboratorio B', 'AMD Ryzen 5, 16GB RAM, SSD 512GB', 'Disponible'],
            ['PC-LAB-07', 'Estación Asus Pro 7', 'Laboratorio B', 'Intel Core i5, 32GB RAM, SSD 1TB', 'Disponible'],
            ['PC-LAB-08', 'Estación Asus Pro 8', 'Laboratorio B', 'Intel Core i5, 16GB RAM, SSD 512GB', 'Mantenimiento'],
        ];

        foreach ($equipos as [$clave, $etiqueta, $sala, $caracteristicas, $estado]) {
            Computadora::updateOrCreate(
                ['clave_equipo' => $clave],
                [
                    'etiqueta' => $etiqueta,
                    'sala' => $sala,
                    'caracteristicas' => $caracteristicas,
                    'estado' => $estado,
                ]
            );
        }
    }
}
