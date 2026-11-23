<?php

namespace App\Http\Controllers\Practica1_5;

use App\Http\Controllers\Controller;
use App\Models\Computadora;
use App\Models\SolicitudReserva;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudReservaController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        $consulta = SolicitudReserva::with(['usuario', 'computadora'])->latest();

        if ($usuario->esRol('estudiante')) {
            $consulta->where('user_id', $usuario->id);
        }

        $reservas = $consulta->get();

        return view('practica1_5.reservas.index', compact('reservas'));
    }

    public function show(Request $request, SolicitudReserva $reserva): View
    {
        $usuario = $request->user();

        if ($usuario->esRol('estudiante') && $reserva->user_id !== $usuario->id) {
            abort(403, 'No tienes permiso para consultar reservaciones de otro usuario.');
        }

        $reserva->load(['usuario', 'computadora']);

        return view('practica1_5.reservas.show', compact('reserva'));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'computadora_id' => ['required', 'integer', 'exists:computadoras,id'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'inicio' => ['required', 'date_format:H:i'],
            'fin' => ['required', 'date_format:H:i', 'after:inicio'],
        ], [
            'computadora_id.required' => 'Selecciona un equipo.',
            'computadora_id.exists' => 'El equipo seleccionado no existe.',
            'fecha.required' => 'Selecciona la fecha de reservación.',
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'inicio.required' => 'Indica la hora de inicio.',
            'inicio.date_format' => 'Formato de hora de inicio inválido.',
            'fin.required' => 'Indica la hora de fin.',
            'fin.date_format' => 'Formato de hora de fin inválido.',
            'fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        $usuario = $request->user();

        $resultado = DB::transaction(function () use ($datos, $usuario) {
            $computadora = Computadora::whereKey($datos['computadora_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $tieneReservaActiva = SolicitudReserva::where('computadora_id', $computadora->id)
                ->where('estado', 'Activa')
                ->exists();

            if ($computadora->estado !== 'Disponible' || $tieneReservaActiva) {
                return null;
            }

            $reserva = SolicitudReserva::create([
                'user_id' => $usuario->id,
                'computadora_id' => $computadora->id,
                'fecha' => $datos['fecha'],
                'inicio' => $datos['inicio'],
                'fin' => $datos['fin'],
                'estado' => 'Activa',
            ]);

            $computadora->update(['estado' => 'Reservado']);

            return $reserva;
        });

        if (! $resultado) {
            return redirect()->route('equipos.index')
                ->with('error', 'Este equipo ya no está disponible porque fue reservado.');
        }

        return redirect()->route('reservas.index')
            ->with('exito', 'Equipo reservado correctamente.');
    }

    public function cancelar(Request $request, SolicitudReserva $reserva): RedirectResponse
    {
        $usuario = $request->user();

        if ($usuario->esRol('estudiante') && $reserva->user_id !== $usuario->id) {
            abort(403, 'No tienes permiso para cancelar reservaciones de otro usuario.');
        }

        $cancelada = DB::transaction(function () use ($reserva) {
            $reservaBloqueada = SolicitudReserva::whereKey($reserva->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservaBloqueada->estado !== 'Activa') {
                return false;
            }

            $computadora = Computadora::whereKey($reservaBloqueada->computadora_id)
                ->lockForUpdate()
                ->first();

            $reservaBloqueada->update(['estado' => 'Cancelada']);

            if ($computadora && $computadora->estado === 'Reservado') {
                $otraActiva = SolicitudReserva::where('computadora_id', $computadora->id)
                    ->where('estado', 'Activa')
                    ->exists();

                if (! $otraActiva) {
                    $computadora->update(['estado' => 'Disponible']);
                }
            }

            return true;
        });

        if (! $cancelada) {
            return back()->with('error', 'Solo se pueden cancelar reservaciones activas.');
        }

        return redirect()->route('reservas.index')
            ->with('exito', 'Reservación cancelada y equipo liberado.');
    }
}
