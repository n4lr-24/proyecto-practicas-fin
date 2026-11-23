<?php

namespace App\Http\Controllers\Practica1_5;

use App\Http\Controllers\Controller;
use App\Models\Computadora;
use App\Models\SolicitudReserva;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudReservaController extends Controller
{
    private const DURACION_MIN_MINUTOS = 30;

    private const DURACION_MAX_MINUTOS = 240;

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

        $inicioDt = Carbon::createFromFormat('Y-m-d H:i', "{$datos['fecha']} {$datos['inicio']}");
        $finDt = Carbon::createFromFormat('Y-m-d H:i', "{$datos['fecha']} {$datos['fin']}");

        if ($inicioDt->lt(now())) {
            return redirect()->route('equipos.index')
                ->with('error', 'No puedes reservar en una fecha u horario que ya pasó.');
        }

        $duracionMinutos = $inicioDt->diffInMinutes($finDt);

        if ($duracionMinutos < self::DURACION_MIN_MINUTOS || $duracionMinutos > self::DURACION_MAX_MINUTOS) {
            return redirect()->route('equipos.index')
                ->with('error', 'La reservación debe durar entre 30 minutos y 4 horas.');
        }

        $usuario = $request->user();

        $resultado = DB::transaction(function () use ($datos, $usuario) {
            $computadora = Computadora::whereKey($datos['computadora_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($computadora->estado === 'Mantenimiento') {
                return 'mantenimiento';
            }

            $solapada = SolicitudReserva::where('computadora_id', $computadora->id)
                ->where('estado', 'Activa')
                ->whereDate('fecha', $datos['fecha'])
                ->where('inicio', '<', $datos['fin'])
                ->where('fin', '>', $datos['inicio'])
                ->exists();

            if ($solapada) {
                return 'solapada';
            }

            SolicitudReserva::create([
                'user_id' => $usuario->id,
                'computadora_id' => $computadora->id,
                'fecha' => $datos['fecha'],
                'inicio' => $datos['inicio'],
                'fin' => $datos['fin'],
                'estado' => 'Activa',
            ]);

            $computadora->update(['estado' => 'Reservado']);

            return 'ok';
        });

        if ($resultado === 'mantenimiento') {
            return redirect()->route('equipos.index')
                ->with('error', 'Este equipo está en mantenimiento y no se puede reservar.');
        }

        if ($resultado === 'solapada') {
            return redirect()->route('equipos.index')
                ->with('error', 'Este equipo ya tiene una reservación activa en ese horario.');
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

        $resultado = DB::transaction(function () use ($reserva, $usuario) {
            $reservaBloqueada = SolicitudReserva::whereKey($reserva->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($reservaBloqueada->estado !== 'Activa') {
                return 'inactiva';
            }

            $inicioDt = Carbon::parse($reservaBloqueada->fecha->format('Y-m-d').' '.$reservaBloqueada->inicio);

            if ($usuario->esRol('estudiante') && $inicioDt->lte(now())) {
                return 'iniciada';
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

            return 'ok';
        });

        if ($resultado === 'inactiva') {
            return back()->with('error', 'Solo se pueden cancelar reservaciones activas.');
        }

        if ($resultado === 'iniciada') {
            return back()->with('error', 'No puedes cancelar una reservación que ya inició o finalizó.');
        }

        return redirect()->route('reservas.index')
            ->with('exito', 'Reservación cancelada correctamente.');
    }
}
