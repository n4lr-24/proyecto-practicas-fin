<?php

namespace App\Http\Controllers\Practica1_3;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalificacionController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        $camposPermitidos = ['estudiante_nombre', 'calificacion', 'inasistencias'];
        $ordenar = in_array($request->query('ordenar'), $camposPermitidos, true)
            ? $request->query('ordenar')
            : 'estudiante_nombre';

        $direccion = in_array($request->query('direccion'), ['asc', 'desc'], true)
            ? $request->query('direccion')
            : 'asc';

        $consulta = Calificacion::query();

        if ($usuario->esRol(RolUsuario::ALUMNO)) {
            $consulta->where('alumno_user_id', $usuario->id);
        }

        if ($ordenar === 'estudiante_nombre') {
            $consulta->orderByRaw("LOWER(estudiante_nombre) {$direccion}");
        } else {
            $consulta->orderBy($ordenar, $direccion)
                ->orderByRaw('LOWER(estudiante_nombre) asc');
        }

        $calificaciones = $consulta->get();

        return view('practica1_3.index', compact('calificaciones', 'ordenar', 'direccion'));
    }

    public function create(): View
    {
        $alumnos = User::where('rol', RolUsuario::ALUMNO->value)
            ->orderBy('name')
            ->get();

        return view('practica1_3.create', compact('alumnos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'alumno_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'estudiante_nombre' => ['required', 'string', 'max:120'],
            'inasistencias' => ['required', 'integer', 'min:0', 'max:100'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'estudiante_nombre.required' => 'El nombre del alumno es obligatorio.',
            'inasistencias.required' => 'El número de faltas es obligatorio.',
            'inasistencias.integer' => 'Las faltas deben ser un número entero.',
            'inasistencias.min' => 'Las faltas no pueden ser negativas.',
            'inasistencias.max' => 'Las faltas no pueden superar 100.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser numérica.',
            'calificacion.min' => 'La calificación mínima permitida es 0.',
            'calificacion.max' => 'La calificación máxima permitida es 10.',
        ]);

        Calificacion::create([
            'alumno_user_id' => $datos['alumno_user_id'] ?: null,
            'estudiante_nombre' => $datos['estudiante_nombre'],
            'inasistencias' => $datos['inasistencias'],
            'calificacion' => $datos['calificacion'],
            'equivalencia' => Calificacion::obtenerEquivalencia((float) $datos['calificacion']),
        ]);

        return redirect()->route('calificaciones.index')
            ->with('exito', 'Registro académico guardado correctamente.');
    }

    public function edit(Calificacion $calificacion): View
    {
        $alumnos = User::where('rol', RolUsuario::ALUMNO->value)
            ->orderBy('name')
            ->get();

        return view('practica1_3.edit', compact('calificacion', 'alumnos'));
    }

    public function update(Request $request, Calificacion $calificacion): RedirectResponse
    {
        $datos = $request->validate([
            'alumno_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'estudiante_nombre' => ['required', 'string', 'max:120'],
            'inasistencias' => ['required', 'integer', 'min:0', 'max:100'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'estudiante_nombre.required' => 'El nombre del alumno es obligatorio.',
            'inasistencias.required' => 'El número de faltas es obligatorio.',
            'inasistencias.integer' => 'Las faltas deben ser un número entero.',
            'inasistencias.min' => 'Las faltas no pueden ser negativas.',
            'inasistencias.max' => 'Las faltas no pueden superar 100.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser numérica.',
            'calificacion.min' => 'La calificación mínima permitida es 0.',
            'calificacion.max' => 'La calificación máxima permitida es 10.',
        ]);

        $calificacion->update([
            'alumno_user_id' => $datos['alumno_user_id'] ?: null,
            'estudiante_nombre' => $datos['estudiante_nombre'],
            'inasistencias' => $datos['inasistencias'],
            'calificacion' => $datos['calificacion'],
            'equivalencia' => Calificacion::obtenerEquivalencia((float) $datos['calificacion']),
        ]);

        return redirect()->route('calificaciones.index')
            ->with('exito', 'Registro académico actualizado correctamente.');
    }

    public function destroy(Calificacion $calificacion): RedirectResponse
    {
        $calificacion->delete();

        return redirect()->route('calificaciones.index')
            ->with('exito', 'Registro eliminado correctamente.');
    }
}
