<?php

namespace App\Enums;

enum RolUsuario: string
{
    case ADMINISTRADOR = 'administrador';
    case EMPLEADO = 'empleado';
    case PROFESOR = 'profesor';
    case ALUMNO = 'alumno';
    case ESTUDIANTE = 'estudiante';
    case JUGADOR = 'jugador';

    public function etiqueta(): string
    {
        return match ($this) {
            self::ADMINISTRADOR => 'Administrador',
            self::EMPLEADO => 'Empleado',
            self::PROFESOR => 'Profesor',
            self::ALUMNO => 'Alumno',
            self::ESTUDIANTE => 'Estudiante',
            self::JUGADOR => 'Jugador',
        };
    }

    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
