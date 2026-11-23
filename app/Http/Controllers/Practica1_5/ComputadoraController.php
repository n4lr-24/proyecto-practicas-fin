<?php

namespace App\Http\Controllers\Practica1_5;

use App\Http\Controllers\Controller;
use App\Models\Computadora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComputadoraController extends Controller
{
    public function index(): View
    {
        $computadoras = Computadora::with([
            'solicitudesReserva' => function ($query) {
                $query->where('estado', 'Activa')
                    ->whereDate('fecha', '>=', now()->toDateString())
                    ->orderBy('fecha')
                    ->orderBy('inicio');
            },
        ])->orderBy('clave_equipo')->get();

        return view('practica1_5.equipos.index', compact('computadoras'));
    }

    public function create(): View
    {
        return view('practica1_5.equipos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'clave_equipo' => ['required', 'string', 'max:50', 'unique:computadoras,clave_equipo'],
            'etiqueta' => ['required', 'string', 'max:120'],
            'sala' => ['required', 'string', 'max:100'],
            'caracteristicas' => ['required', 'string', 'max:500'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ], [
            'clave_equipo.required' => 'La clave del equipo es obligatoria.',
            'clave_equipo.unique' => 'Ya existe un equipo con esa clave.',
            'etiqueta.required' => 'El nombre o etiqueta es obligatorio.',
            'sala.required' => 'La sala o laboratorio es obligatoria.',
            'caracteristicas.required' => 'Las características son obligatorias.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        Computadora::create($datos);

        return redirect()->route('equipos.index')
            ->with('exito', 'Equipo registrado correctamente.');
    }

    public function edit(Computadora $computadora): View
    {
        return view('practica1_5.equipos.edit', compact('computadora'));
    }

    public function update(Request $request, Computadora $computadora): RedirectResponse
    {
        $datos = $request->validate([
            'clave_equipo' => ['required', 'string', 'max:50', Rule::unique('computadoras', 'clave_equipo')->ignore($computadora->id)],
            'etiqueta' => ['required', 'string', 'max:120'],
            'sala' => ['required', 'string', 'max:100'],
            'caracteristicas' => ['required', 'string', 'max:500'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ], [
            'clave_equipo.required' => 'La clave del equipo es obligatoria.',
            'clave_equipo.unique' => 'Ya existe otro equipo con esa clave.',
            'etiqueta.required' => 'El nombre o etiqueta es obligatorio.',
            'sala.required' => 'La sala o laboratorio es obligatoria.',
            'caracteristicas.required' => 'Las características son obligatorias.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        $tieneReservasActivas = $computadora->solicitudesReserva()
            ->where('estado', 'Activa')
            ->exists();

        if ($tieneReservasActivas && $datos['estado'] !== 'Reservado') {
            return back()
                ->withErrors(['estado' => 'Este equipo tiene reservaciones activas. Cancélalas antes de cambiar su estado.'])
                ->withInput();
        }

        if (! $tieneReservasActivas && $datos['estado'] === 'Reservado') {
            return back()
                ->withErrors(['estado' => 'No puedes marcar manualmente como Reservado un equipo sin reservaciones activas.'])
                ->withInput();
        }

        $computadora->update($datos);

        return redirect()->route('equipos.index')
            ->with('exito', 'Equipo actualizado correctamente.');
    }

    public function destroy(Computadora $computadora): RedirectResponse
    {
        $tieneReservasActivas = $computadora->solicitudesReserva()
            ->where('estado', 'Activa')
            ->exists();

        if ($tieneReservasActivas) {
            return back()->with('error', 'No puedes eliminar un equipo que tiene reservaciones activas.');
        }

        $computadora->delete();

        return redirect()->route('equipos.index')
            ->with('exito', 'Equipo eliminado correctamente.');
    }
}
