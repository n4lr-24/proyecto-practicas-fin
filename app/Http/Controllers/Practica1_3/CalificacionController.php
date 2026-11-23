<?php

namespace App\Http\Controllers\Practica1_3;

use App\Enums\RolUsuario;
use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalificacionController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();
        $esAlumno = $usuario->esRol(RolUsuario::ALUMNO);

        $camposPermitidos = $esAlumno
            ? ['unidad', 'calificacion', 'inasistencias']
            : ['estudiante_nombre', 'calificacion', 'inasistencias'];

        $campoDefault = $esAlumno ? 'unidad' : 'estudiante_nombre';

        $ordenar = in_array($request->query('ordenar'), $camposPermitidos, true)
            ? $request->query('ordenar')
            : $campoDefault;

        $direccion = in_array($request->query('direccion'), ['asc', 'desc'], true)
            ? $request->query('direccion')
            : 'asc';

        $unidad = $request->query('unidad');
        $unidadFiltro = (! $esAlumno && in_array((int) $unidad, [1, 2, 3, 4], true)) ? (int) $unidad : null;

        $consulta = Calificacion::query()->with('alumno');

        if ($esAlumno) {
            $consulta->where('alumno_user_id', $usuario->id);
        }

        if ($unidadFiltro !== null) {
            $consulta->where('unidad', $unidadFiltro);
        }

        if ($ordenar === 'estudiante_nombre') {
            $consulta->orderByRaw("LOWER(estudiante_nombre) {$direccion}")
                ->orderBy('unidad', 'asc');
        } else {
            $consulta->orderBy($ordenar, $direccion)
                ->orderByRaw('LOWER(estudiante_nombre) asc');
        }

        $calificaciones = $consulta->get();

        return view('practica1_3.index', compact('calificaciones', 'ordenar', 'direccion', 'unidadFiltro'));
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
            'alumno_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('rol', RolUsuario::ALUMNO->value),
            ],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'inasistencias' => ['required', 'integer', 'min:0', 'max:5'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'alumno_user_id.required' => 'Selecciona un alumno registrado.',
            'alumno_user_id.exists' => 'El alumno seleccionado no es válido.',
            'unidad.required' => 'Selecciona la unidad.',
            'unidad.integer' => 'La unidad debe ser un número entero.',
            'unidad.between' => 'La unidad debe estar entre 1 y 4.',
            'inasistencias.required' => 'El número de faltas es obligatorio.',
            'inasistencias.integer' => 'Las faltas deben ser un número entero.',
            'inasistencias.min' => 'Las faltas no pueden ser negativas.',
            'inasistencias.max' => 'Las faltas por unidad no pueden superar 5.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser numérica.',
            'calificacion.min' => 'La calificación mínima permitida es 0.',
            'calificacion.max' => 'La calificación máxima permitida es 10.',
        ]);

        $alumno = User::findOrFail($datos['alumno_user_id']);

        if ($this->existeUnidadParaAlumno($alumno->id, (int) $datos['unidad'])) {
            return back()
                ->withErrors(['unidad' => "{$alumno->name} ya tiene calificación registrada en la Unidad {$datos['unidad']}."])
                ->withInput();
        }

        try {
            Calificacion::create([
                'alumno_user_id' => $alumno->id,
                'estudiante_nombre' => $alumno->name,
                'unidad' => (int) $datos['unidad'],
                'inasistencias' => (int) $datos['inasistencias'],
                'calificacion' => $datos['calificacion'],
                'equivalencia' => Calificacion::obtenerEquivalencia((float) $datos['calificacion']),
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withErrors(['unidad' => "{$alumno->name} ya tiene calificación registrada en la Unidad {$datos['unidad']}."])
                ->withInput();
        }

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
            'alumno_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('rol', RolUsuario::ALUMNO->value),
            ],
            'unidad' => ['required', 'integer', 'between:1,4'],
            'inasistencias' => ['required', 'integer', 'min:0', 'max:5'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:10'],
        ], [
            'alumno_user_id.required' => 'Selecciona un alumno registrado.',
            'alumno_user_id.exists' => 'El alumno seleccionado no es válido.',
            'unidad.required' => 'Selecciona la unidad.',
            'unidad.integer' => 'La unidad debe ser un número entero.',
            'unidad.between' => 'La unidad debe estar entre 1 y 4.',
            'inasistencias.required' => 'El número de faltas es obligatorio.',
            'inasistencias.integer' => 'Las faltas deben ser un número entero.',
            'inasistencias.min' => 'Las faltas no pueden ser negativas.',
            'inasistencias.max' => 'Las faltas por unidad no pueden superar 5.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser numérica.',
            'calificacion.min' => 'La calificación mínima permitida es 0.',
            'calificacion.max' => 'La calificación máxima permitida es 10.',
        ]);

        $alumno = User::findOrFail($datos['alumno_user_id']);

        if ($this->existeUnidadParaAlumno($alumno->id, (int) $datos['unidad'], $calificacion->id)) {
            return back()
                ->withErrors(['unidad' => "{$alumno->name} ya tiene calificación registrada en la Unidad {$datos['unidad']}."])
                ->withInput();
        }

        try {
            $calificacion->update([
                'alumno_user_id' => $alumno->id,
                'estudiante_nombre' => $alumno->name,
                'unidad' => (int) $datos['unidad'],
                'inasistencias' => (int) $datos['inasistencias'],
                'calificacion' => $datos['calificacion'],
                'equivalencia' => Calificacion::obtenerEquivalencia((float) $datos['calificacion']),
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()
                ->withErrors(['unidad' => "{$alumno->name} ya tiene calificación registrada en la Unidad {$datos['unidad']}."])
                ->withInput();
        }

        return redirect()->route('calificaciones.index')
            ->with('exito', 'Registro académico actualizado correctamente.');
    }

    public function destroy(Calificacion $calificacion): RedirectResponse
    {
        $calificacion->delete();

        return redirect()->route('calificaciones.index')
            ->with('exito', 'Registro eliminado correctamente.');
    }

    private function existeUnidadParaAlumno(int $alumnoUserId, int $unidad, ?int $ignorarId = null): bool
    {
        $consulta = Calificacion::where('alumno_user_id', $alumnoUserId)
            ->where('unidad', $unidad);

        if ($ignorarId !== null) {
            $consulta->where('id', '!=', $ignorarId);
        }

        return $consulta->exists();
    }
}
