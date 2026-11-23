@extends('layouts.app')

@section('titulo', 'Juego del Gato')
@section('encabezado', 'Práctica 1.6 (Juego del Gato)')

@section('contenido')
    @php
        $esJugador = auth()->user()->esRol('jugador');
        $esAdmin = auth()->user()->esRol('administrador');
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <a href="{{ route('gato.historial') }}"
           class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
            {{ $esAdmin ? 'Historial general' : 'Mi historial' }}
        </a>

        @if ($esJugador)
            <form method="POST" action="{{ route('gato.store') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">
                    + Nueva partida
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            @if ($esJugador)
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                    <h2 class="text-sm font-semibold text-slate-900 mb-3">Partidas disponibles para unirse</h2>
                    @forelse ($salasDisponibles as $sala)
                        <div class="flex items-center justify-between py-2.5 border-t border-slate-100 first:border-t-0">
                            <div>
                                <p class="text-sm font-medium text-slate-900">Sala #{{ $sala->id }}</p>
                                <p class="text-xs text-slate-500">Creada por {{ $sala->retadorX->name }} (X)</p>
                            </div>
                            <form method="POST" action="{{ route('gato.entrar', $sala) }}">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-medium hover:bg-slate-800">
                                    Unirme (O)
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No hay salas esperando jugador.</p>
                    @endforelse
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                <h2 class="text-sm font-semibold text-slate-900 mb-3">
                    {{ $esAdmin ? 'Todas las partidas activas' : 'Mis partidas activas' }}
                </h2>
                @forelse ($salasActivas as $sala)
                    <div class="flex flex-wrap items-center justify-between gap-2 py-2.5 border-t border-slate-100 first:border-t-0">
                        <div>
                            <p class="text-sm font-medium text-slate-900">
                                Sala #{{ $sala->id }} · {{ $sala->retadorX->name }} (X) vs {{ $sala->retadorO?->name ?? 'Esperando...' }} (O)
                            </p>
                            <p class="text-xs text-slate-500">
                                Estado: {{ $sala->estado === 'esperando' ? 'Esperando rival' : 'En curso · Turno ' . $sala->turno }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('gato.show', $sala) }}"
                               class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                {{ $esAdmin ? 'Ver' : 'Abrir tablero' }}
                            </a>
                            @if ($esAdmin)
                                <form method="POST" action="{{ route('gato.cancelar', $sala) }}"
                                      onsubmit="return confirm('¿Cancelar la partida #{{ $sala->id }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-rose-300 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No hay partidas activas.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 h-fit">
            <h2 class="text-sm font-semibold text-slate-900 mb-3">Jugadores registrados</h2>
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($jugadoresDisponibles as $jugador)
                    <li class="py-2 flex items-center justify-between">
                        <span class="font-medium text-slate-800">{{ $jugador->name }}</span>
                        <span class="text-xs text-slate-400">{{ $jugador->email }}</span>
                    </li>
                @empty
                    <li class="py-2 text-slate-500">No hay otros jugadores registrados.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
