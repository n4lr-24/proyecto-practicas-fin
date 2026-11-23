@extends('layouts.app')

@section('titulo', 'Historial')
@section('encabezado', 'Práctica 1.6 (Historial)')

@section('contenido')
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-600">Total de partidas: <strong class="text-slate-900">{{ $partidas->count() }}</strong></p>
        <a href="{{ route('gato.index') }}"
           class="px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-700 hover:bg-slate-100">
            &larr; Volver a salas
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Sala</th>
                        <th class="py-3 px-4">Jugador X</th>
                        <th class="py-3 px-4">Jugador O</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4">Resultado</th>
                        <th class="py-3 px-4">Fecha</th>
                        <th class="py-3 px-4 text-right">Tablero</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($partidas as $partida)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-xs text-slate-500">#{{ $partida->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $partida->retadorX->name }}</td>
                            <td class="py-3 px-4 text-slate-700">{{ $partida->retadorO?->name ?? '—' }}</td>
                            <td class="py-3 px-4 capitalize text-slate-700">{{ str_replace('_', ' ', $partida->estado) }}</td>
                            <td class="py-3 px-4">
                                @if ($partida->ganador)
                                    <span class="font-medium text-emerald-700">Ganó {{ $partida->ganador->name }}</span>
                                @elseif ($partida->estado === 'finalizada')
                                    <span class="text-amber-700 font-medium">Empate</span>
                                @elseif ($partida->estado === 'cancelada')
                                    <span class="text-rose-700">Cancelada</span>
                                @elseif ($partida->estado === 'abandonada')
                                    <span class="text-slate-500">Abandonada</span>
                                @else
                                    <span class="text-slate-400">En progreso</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-500">{{ $partida->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('gato.show', $partida) }}"
                                   class="px-2.5 py-1 rounded-md border border-slate-300 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500">No hay partidas en el historial.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
