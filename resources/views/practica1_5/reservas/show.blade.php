@extends('layouts.app')

@section('titulo', 'Detalle de reservación')
@section('encabezado', 'Práctica 1.5 (Detalle de reservación)')

@section('contenido')
    <div class="max-w-xl bg-white border border-slate-200 rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-start justify-between border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Reservación #{{ $reserva->id }}</h2>
                <p class="text-sm text-slate-500">{{ $reserva->computadora->clave_equipo }} · {{ $reserva->computadora->etiqueta }}</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-slate-100 text-slate-800 border-slate-200">
                {{ $reserva->estado }}
            </span>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Usuario</dt>
                <dd class="text-slate-800 mt-0.5">{{ $reserva->usuario->name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Sala</dt>
                <dd class="text-slate-800 mt-0.5">{{ $reserva->computadora->sala }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Fecha</dt>
                <dd class="text-slate-800 mt-0.5">{{ $reserva->fecha->format('d/m/Y') }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase text-slate-400 font-semibold">Horario</dt>
                <dd class="text-slate-800 font-mono mt-0.5">{{ substr($reserva->inicio, 0, 5) }} - {{ substr($reserva->fin, 0, 5) }}</dd>
            </div>
        </dl>

        <div class="pt-4 border-t border-slate-200 flex items-center gap-2">
            @if ($reserva->estado === 'Activa')
                <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}"
                      onsubmit="return confirm('¿Cancelar esta reservación?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-medium hover:bg-rose-50">
                        Cancelar reservación
                    </button>
                </form>
            @endif
            <a href="{{ route('reservas.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-100">
                Volver
            </a>
        </div>
    </div>
@endsection
